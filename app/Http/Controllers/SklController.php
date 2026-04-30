<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Rapor;
use App\Models\Usp;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SklController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::withCount(['students' => function ($query) {
            $query->where('is_active', true);
        }])->paginate(10);
        return view('skl.index', compact('classes'));
    }

    public function show(Request $request, SchoolClass $class)
    {
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();

        $existingGradesRaw = \DB::table('rapors')
            ->where('school_class_id', $class->id)
            ->get();
            
        // Aggregate: jumlahkan seluruh nilai mapel X dari siswa Y, lalu bagi jumlah semester yang diikuti
        $aggregatedGrades = [];
        foreach ($existingGradesRaw as $grade) {
            $key = $grade->student_id . '_' . $grade->subject_id;
            if (!isset($aggregatedGrades[$key])) {
                $aggregatedGrades[$key] = ['total' => 0, 'semesters' => []];
            }
            $aggregatedGrades[$key]['total'] += floatval($grade->grade);
            if (!in_array($grade->semester_id, $aggregatedGrades[$key]['semesters'])) {
                $aggregatedGrades[$key]['semesters'][] = $grade->semester_id;
            }
        }
        
        $existingGrades = [];
        foreach ($aggregatedGrades as $key => $data) {
            $semesterCount = count($data['semesters']);
            $existingGrades[$key] = (object) ['grade' => $semesterCount > 0 ? ($data['total'] / $semesterCount) : 0];
        }

        // Fetch USP grades
        $uspGrades = \DB::table('usps')
            ->where('school_class_id', $class->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->student_id . '_' . $item->subject_id;
            });

        return view('skl.show', compact('class', 'students', 'subjects', 'existingGrades', 'uspGrades'));
    }

    public function exportExcel(Request $request, SchoolClass $class)
    {
        $min_grade = floatval($request->get('min_grade', 65));

        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();

        $existingGradesRaw = \DB::table('rapors')
            ->where('school_class_id', $class->id)
            ->get();
            
        // Aggregate: jumlahkan seluruh nilai mapel X dari siswa Y, lalu bagi jumlah semester yang diikuti
        $aggregatedGrades = [];
        foreach ($existingGradesRaw as $grade) {
            $key = $grade->student_id . '_' . $grade->subject_id;
            if (!isset($aggregatedGrades[$key])) {
                $aggregatedGrades[$key] = ['total' => 0, 'semesters' => []];
            }
            $aggregatedGrades[$key]['total'] += floatval($grade->grade);
            if (!in_array($grade->semester_id, $aggregatedGrades[$key]['semesters'])) {
                $aggregatedGrades[$key]['semesters'][] = $grade->semester_id;
            }
        }
        
        $existingGrades = [];
        foreach ($aggregatedGrades as $key => $data) {
            $semesterCount = count($data['semesters']);
            $existingGrades[$key] = (object) ['grade' => $semesterCount > 0 ? ($data['total'] / $semesterCount) : 0];
        }

        // Fetch USP grades
        $uspGrades = \DB::table('usps')
            ->where('school_class_id', $class->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->student_id . '_' . $item->subject_id;
            });

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data SKL Kumulatif');

        $headers = ['NO. URUT', 'NO. INDUK', 'NAMA PESERTA USP', 'L/P'];
        foreach ($subjects as $subject) {
            $headers[] = $subject->name;
        }
        $headers[] = 'RATA-RATA NILAI RAPOR SEMUA MAPEL';
        $headers[] = 'RATA-RATA USP';
        $headers[] = 'RATA-RATA AKHIR';
        $headers[] = 'NILAI SIKAP/PERILAKU (SB; B; C; K)';
        $headers[] = 'KET (LULUS, TIDAK LULUS)';

        $sheet->fromArray($headers, null, 'A1');
        // Make headers bold
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->getFont()->setBold(true);

        $rowNum = 2;
        $noUrut = 1;

        foreach ($students as $student) {
            $gender = 'L'; // Default fallback
            if (strtolower(substr($student->gender, 0, 1)) === 'p') {
                $gender = 'P';
            } elseif (strtolower(substr($student->gender, 0, 1)) === 'l') {
                $gender = 'L';
            }

            $rowData = [
                $noUrut++,
                $student->nis ?? '-',
                $student->name,
                $gender
            ];

            $totalGrade = 0;
            $totalUsp = 0;
            $totalRataAkhir = 0;
            $countGrade = 0;

            foreach ($subjects as $subject) {
                $key = $student->id . '_' . $subject->id;
                $gradeRapor = isset($existingGrades[$key]) ? floatval($existingGrades[$key]->grade) : 0;
                $gradeUsp = isset($uspGrades[$key]) ? floatval($uspGrades[$key]->grade) : 0;
                $rataAkhir = ($gradeRapor + $gradeUsp) / 2;

                $rowData[] = round($rataAkhir);
                $totalGrade += $gradeRapor;
                $totalUsp += $gradeUsp;
                $totalRataAkhir += $rataAkhir;
                $countGrade++;
            }

            $avgRapor = $countGrade > 0 ? ($totalGrade / $countGrade) : 0;
            $avgUsp = $countGrade > 0 ? ($totalUsp / $countGrade) : 0;
            $avgRataAkhir = $countGrade > 0 ? ($totalRataAkhir / $countGrade) : 0;
            
            $rowData[] = round($avgRapor, 2);
            $rowData[] = round($avgUsp, 2);
            $rowData[] = round($avgRataAkhir, 2);

            // Behavioral grade
            $rowData[] = 'B';

            // Pass status based on rata-rata akhir
            if ($avgRataAkhir >= $min_grade) {
                $rowData[] = 'L';
            } else {
                $rowData[] = 'TL';
            }

            $sheet->fromArray($rowData, null, 'A' . $rowNum);
            $rowNum++;
        }

        foreach(range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'SKL_Kumulatif_Kelas_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $class->name) . '_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function cetakIndex()
    {
        $classes = SchoolClass::withCount(['students' => function ($query) {
            $query->where('is_active', true);
        }])->paginate(10);
        
        $tempatCetak = Setting::where('key', 'tempat_cetak')->first()->value ?? 'Pacitan';
        $tanggalCetak = Setting::where('key', 'tanggal_cetak')->first()->value ?? date('Y-m-d');
        $nomorSkl = Setting::where('key', 'nomor_skl')->first()->value ?? '400.3.11.1/059/408.37.10.50/' . date('Y');
        
        return view('skl.cetak_index', compact('classes', 'tempatCetak', 'tanggalCetak', 'nomorSkl'));
    }

    public function saveCetakSettings(Request $request)
    {
        $request->validate([
            'tempat_cetak' => 'required|string',
            'tanggal_cetak' => 'required|date',
            'nomor_skl' => 'required|string'
        ]);

        Setting::updateOrCreate(['key' => 'tempat_cetak'], ['value' => $request->tempat_cetak]);
        Setting::updateOrCreate(['key' => 'tanggal_cetak'], ['value' => $request->tanggal_cetak]);
        Setting::updateOrCreate(['key' => 'nomor_skl'], ['value' => $request->nomor_skl]);

        return redirect()->route('skl.cetak.index')->with('success', 'Pengaturan cetak berhasil disimpan.');
    }

    public function cetakShow(Request $request, SchoolClass $class)
    {
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        return view('skl.cetak_show', compact('class', 'students'));
    }

    public function cetakPdf(Student $student)
    {
        $class = $student->schoolClass;
        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();
        
        $existingGradesRaw = \DB::table('rapors')
            ->where('student_id', $student->id)
            ->get();

        // Fetch USP grades for this student
        $uspGradesRaw = \DB::table('usps')
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('subject_id');
            
        $existingGrades = [];
        $uspGrades = [];
        $totalRataAkhir = 0;
        $countGrade = 0;
        
        foreach ($subjects as $subject) {
            $subjectGrades = $existingGradesRaw->where('subject_id', $subject->id);
            $total = $subjectGrades->sum('grade');
            $semesterCount = $subjectGrades->pluck('semester_id')->unique()->count();
            $avgRapor = $semesterCount > 0 ? ($total / $semesterCount) : 0;
            
            $existingGrades[$subject->id] = (object) ['grade' => $avgRapor];

            $uspValue = isset($uspGradesRaw[$subject->id]) ? floatval($uspGradesRaw[$subject->id]->grade) : 0;
            $uspGrades[$subject->id] = (object) ['grade' => $uspValue];

            // Rata-Rata Akhir = (Rata-Rata Rapor + USP) / 2
            $rataAkhir = ($avgRapor + $uspValue) / 2;
            
            $totalRataAkhir += $rataAkhir;
            $countGrade++;
        }
        $average = $countGrade > 0 ? ($totalRataAkhir / $countGrade) : 0;
            
        $tempatCetak = Setting::where('key', 'tempat_cetak')->first()->value ?? 'Pacitan';
        $tanggalCetak = Setting::where('key', 'tanggal_cetak')->first()->value ?? date('Y-m-d');
        $nomorSkl = Setting::where('key', 'nomor_skl')->first()->value ?? '400.3.11.1/059/408.37.10.50/' . date('Y');
        
        $websiteLogo = Setting::where('key', 'website_logo')->first()?->value;
        $websiteName = Setting::where('key', 'website_name')->first()?->value ?? 'SIMSiswa';
        $schoolProfile = \Illuminate\Support\Facades\DB::table('school_profiles')->first();

        $kepalaSekolah = \Illuminate\Support\Facades\DB::table('teachers')->where('position', 'Kepala Sekolah')->first();
        
        $isLulus = $average >= 65; 
        
        $pdf = Pdf::loadView('skl.pdf', compact('student', 'class', 'subjects', 'existingGrades', 'uspGrades', 'tempatCetak', 'tanggalCetak', 'nomorSkl', 'websiteLogo', 'websiteName', 'kepalaSekolah', 'average', 'isLulus', 'schoolProfile'));
        
        return $pdf->stream('SKL_' . $student->nis . '_' . $student->name . '.pdf');
    }

    /**
     * Nilai Akhir: Pilih kelas
     */
    public function nilaiAkhirIndex()
    {
        $classes = SchoolClass::withCount(['students' => function ($query) {
            $query->where('is_active', true);
        }])->paginate(10);
        return view('skl.nilai_akhir_index', compact('classes'));
    }

    /**
     * Nilai Akhir: Tampilkan per kelas
     * NA per mapel = (Rata-rata Rapor * 60%) + (Nilai USP * 40%)
     */
    public function nilaiAkhirShow(Request $request, SchoolClass $class)
    {
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();

        // Aggregate rapor grades: average per subject per student across all semesters
        $existingGradesRaw = \DB::table('rapors')
            ->where('school_class_id', $class->id)
            ->get();
            
        $aggregatedGrades = [];
        foreach ($existingGradesRaw as $grade) {
            $key = $grade->student_id . '_' . $grade->subject_id;
            if (!isset($aggregatedGrades[$key])) {
                $aggregatedGrades[$key] = ['total' => 0, 'semesters' => []];
            }
            $aggregatedGrades[$key]['total'] += floatval($grade->grade);
            if (!in_array($grade->semester_id, $aggregatedGrades[$key]['semesters'])) {
                $aggregatedGrades[$key]['semesters'][] = $grade->semester_id;
            }
        }
        
        $existingGrades = [];
        foreach ($aggregatedGrades as $key => $data) {
            $semesterCount = count($data['semesters']);
            $existingGrades[$key] = (object) ['grade' => $semesterCount > 0 ? ($data['total'] / $semesterCount) : 0];
        }

        // Fetch USP grades
        $uspGrades = \DB::table('usps')
            ->where('school_class_id', $class->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->student_id . '_' . $item->subject_id;
            });

        return view('skl.nilai_akhir_show', compact('class', 'students', 'subjects', 'existingGrades', 'uspGrades'));
    }

    /**
     * Public page: Form cek kelulusan siswa
     */
    public function cekKelulusan()
    {
        return view('skl.cek_kelulusan');
    }

    /**
     * Public page: Verifikasi NIS/NISN + tanggal lahir, tampilkan hasil kelulusan
     */
    public function cekKelulusanCheck(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'tanggal_lahir' => 'required|date_format:d/m/Y',
        ], [
            'identifier.required' => 'NIS/NISN wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date_format' => 'Format tanggal lahir harus dd/mm/yyyy.',
        ]);

        $identifier = $request->identifier;
        $tanggalLahir = \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal_lahir)->format('Y-m-d');

        // Cari siswa berdasarkan NIS atau NISN
        $student = Student::where(function ($query) use ($identifier) {
            $query->where('nis', $identifier)
                  ->orWhere('nisn', $identifier);
        })
        ->where('tanggal_lahir', $tanggalLahir)
        ->first();

        if (!$student) {
            return redirect()->route('cek-kelulusan')
                ->withErrors(['identifier' => 'Data siswa tidak ditemukan. Pastikan NIS/NISN dan tanggal lahir sudah benar.'])
                ->withInput();
        }

        // Hitung rata-rata nilai per mapel (sama seperti di cetakPdf)
        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();

        $existingGradesRaw = \DB::table('rapors')
            ->where('student_id', $student->id)
            ->get();

        $uspGradesRaw = \DB::table('usps')
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('subject_id');

        $existingGrades = [];
        $totalRataAkhir = 0;
        $countGrade = 0;

        foreach ($subjects as $subject) {
            $subjectGrades = $existingGradesRaw->where('subject_id', $subject->id);
            $total = $subjectGrades->sum('grade');
            $semesterCount = $subjectGrades->pluck('semester_id')->unique()->count();
            $avgRapor = $semesterCount > 0 ? ($total / $semesterCount) : 0;

            $existingGrades[$subject->id] = (object) ['grade' => $avgRapor];

            $uspValue = isset($uspGradesRaw[$subject->id]) ? floatval($uspGradesRaw[$subject->id]->grade) : 0;
            $rataAkhir = ($avgRapor + $uspValue) / 2;

            $totalRataAkhir += $rataAkhir;
            $countGrade++;
        }
        $average = $countGrade > 0 ? ($totalRataAkhir / $countGrade) : 0;

        $isLulus = $average >= 65;

        return view('skl.cek_kelulusan_result', compact('student', 'subjects', 'existingGrades', 'average', 'isLulus'));
    }
}
