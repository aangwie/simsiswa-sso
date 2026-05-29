<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Rapor;
use App\Models\Usp;
use App\Models\Setting;
use App\Models\FinalGrade;
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

        // Fetch Final Grades
        $finalGrades = \DB::table('final_grades')
            ->where('school_class_id', $class->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->student_id . '_' . $item->subject_id;
            });

        return view('skl.show', compact('class', 'students', 'subjects', 'existingGrades', 'uspGrades', 'finalGrades'));
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

        // Fetch Final Grades
        $finalGrades = \DB::table('final_grades')
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
        $studentsBeforeCount = Student::join('school_classes', 'students.school_class_id', '=', 'school_classes.id')
            ->where('school_classes.grade', $class->grade)
            ->where('school_classes.academic_year', $class->academic_year)
            ->where('school_classes.name', '<', $class->name)
            ->where('students.is_active', true)
            ->count();
        $sklNoUrutAwal = intval(Setting::where('key', 'skl_no_urut_awal')->first()?->value ?? 1);
        $noUrut = $sklNoUrutAwal + $studentsBeforeCount;

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
                $rataAkhir = isset($finalGrades[$key]) ? floatval($finalGrades[$key]->grade) : 0;

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
        
        $tempatCetak = Setting::where('key', 'tempat_cetak')->first()?->value ?? 'Pacitan';
        $tanggalCetak = Setting::where('key', 'tanggal_cetak')->first()?->value ?? date('Y-m-d');
        
        $sklKode = Setting::where('key', 'skl_kode')->first()?->value;
        $sklKodeSekolah = Setting::where('key', 'skl_kode_sekolah')->first()?->value;
        $sklNoUrutAwal = Setting::where('key', 'skl_no_urut_awal')->first()?->value;
        
        // If they are not set yet, migrate/parse them from existing nomor_skl or use defaults
        if (!$sklKode || !$sklKodeSekolah || !$sklNoUrutAwal) {
            $nomorSkl = Setting::where('key', 'nomor_skl')->first()?->value ?? '400.3.11.1/059/408.37.10.50/' . date('Y');
            $parts = explode('/', $nomorSkl);
            if (count($parts) >= 3) {
                $sklKode = $parts[0] . '/';
                $sklNoUrutAwal = intval($parts[1]);
                array_shift($parts);
                array_shift($parts);
                $sklKodeSekolah = '/' . implode('/', $parts);
            } else {
                $sklKode = '400.3.11.1/';
                $sklNoUrutAwal = 59;
                $sklKodeSekolah = '/408.37.10.50/' . date('Y');
            }
            
            Setting::updateOrCreate(['key' => 'skl_kode'], ['value' => $sklKode]);
            Setting::updateOrCreate(['key' => 'skl_no_urut_awal'], ['value' => $sklNoUrutAwal]);
            Setting::updateOrCreate(['key' => 'skl_kode_sekolah'], ['value' => $sklKodeSekolah]);
        }
        
        return view('skl.cetak_index', compact('classes', 'tempatCetak', 'tanggalCetak', 'sklKode', 'sklKodeSekolah', 'sklNoUrutAwal'));
    }

    public function saveCetakSettings(Request $request)
    {
        $request->validate([
            'tempat_cetak' => 'required|string',
            'tanggal_cetak' => 'required|date',
            'skl_kode' => 'required|string',
            'skl_no_urut_awal' => 'required|integer|min:1',
            'skl_kode_sekolah' => 'required|string'
        ]);

        Setting::updateOrCreate(['key' => 'tempat_cetak'], ['value' => $request->tempat_cetak]);
        Setting::updateOrCreate(['key' => 'tanggal_cetak'], ['value' => $request->tanggal_cetak]);
        Setting::updateOrCreate(['key' => 'skl_kode'], ['value' => $request->skl_kode]);
        Setting::updateOrCreate(['key' => 'skl_no_urut_awal'], ['value' => $request->skl_no_urut_awal]);
        Setting::updateOrCreate(['key' => 'skl_kode_sekolah'], ['value' => $request->skl_kode_sekolah]);

        // Keep nomor_skl in sync for backward compatibility
        $paddedNum = str_pad($request->skl_no_urut_awal, 3, '0', STR_PAD_LEFT);
        Setting::updateOrCreate(['key' => 'nomor_skl'], ['value' => $request->skl_kode . $paddedNum . $request->skl_kode_sekolah]);

        return redirect()->route('skl.cetak.index')->with('success', 'Pengaturan cetak berhasil disimpan.');
    }

    public function cetakShow(Request $request, SchoolClass $class)
    {
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $studentsBeforeCount = Student::join('school_classes', 'students.school_class_id', '=', 'school_classes.id')
            ->where('school_classes.grade', $class->grade)
            ->where('school_classes.academic_year', $class->academic_year)
            ->where('school_classes.name', '<', $class->name)
            ->where('students.is_active', true)
            ->count();
            
        $sklNoUrutAwal = intval(Setting::where('key', 'skl_no_urut_awal')->first()?->value ?? 1);
        $startNumber = $sklNoUrutAwal + $studentsBeforeCount;
            
        return view('skl.cetak_show', compact('class', 'students', 'startNumber'));
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

        // Fetch stored Final Grades for this student
        $finalGradesRaw = \DB::table('final_grades')
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('subject_id');
            
        $existingGrades = [];
        $uspGrades = [];
        $finalGrades = [];
        $totalFinalGrade = 0;
        $countGrade = 0;
        
        foreach ($subjects as $subject) {
            $subjectGrades = $existingGradesRaw->where('subject_id', $subject->id);
            $total = $subjectGrades->sum('grade');
            $semesterCount = $subjectGrades->pluck('semester_id')->unique()->count();
            $avgRapor = $semesterCount > 0 ? ($total / $semesterCount) : 0;
            
            $existingGrades[$subject->id] = (object) ['grade' => $avgRapor];

            $uspValue = isset($uspGradesRaw[$subject->id]) ? floatval($uspGradesRaw[$subject->id]->grade) : 0;
            $uspGrades[$subject->id] = (object) ['grade' => $uspValue];

            // Read the stored final grade
            $finalGradeValue = isset($finalGradesRaw[$subject->id]) ? floatval($finalGradesRaw[$subject->id]->grade) : 0;
            $finalGrades[$subject->id] = (object) ['grade' => $finalGradeValue];

            $totalFinalGrade += $finalGradeValue;
            $countGrade++;
        }
        $average = $countGrade > 0 ? ($totalFinalGrade / $countGrade) : 0;
            
        $tempatCetak = Setting::where('key', 'tempat_cetak')->first()?->value ?? 'Pacitan';
        $tanggalCetak = Setting::where('key', 'tanggal_cetak')->first()?->value ?? date('Y-m-d');
        
        $sklKode = Setting::where('key', 'skl_kode')->first()?->value ?? '400.3.11.1/';
        $sklKodeSekolah = Setting::where('key', 'skl_kode_sekolah')->first()?->value ?? ('/408.37.10.50/' . date('Y'));
        $sklNoUrutAwal = intval(Setting::where('key', 'skl_no_urut_awal')->first()?->value ?? 1);
        
        // Find all active students in the same grade and academic year, ordered by class name and student name
        $studentsInGrade = Student::join('school_classes', 'students.school_class_id', '=', 'school_classes.id')
            ->where('school_classes.grade', $class->grade)
            ->where('school_classes.academic_year', $class->academic_year)
            ->where('students.is_active', true)
            ->orderBy('school_classes.name', 'asc')
            ->orderBy('students.name', 'asc')
            ->select('students.*')
            ->get();
            
        $index = $studentsInGrade->pluck('id')->search($student->id);
        $sequenceNumber = str_pad(($index !== false ? $sklNoUrutAwal + $index : $sklNoUrutAwal), 3, '0', STR_PAD_LEFT);
        
        $nomorSkl = $sklKode . $sequenceNumber . $sklKodeSekolah;
        
        $websiteLogo = Setting::where('key', 'website_logo')->first()?->value;
        $websiteName = Setting::where('key', 'website_name')->first()?->value ?? 'SIMSiswa';
        $schoolProfile = \Illuminate\Support\Facades\DB::table('school_profiles')->first();

        $kepalaSekolah = \Illuminate\Support\Facades\DB::table('teachers')->where('position', 'Kepala Sekolah')->first();
        
        $isLulus = $average >= 65; 
        
        $pdf = Pdf::loadView('skl.pdf', compact('student', 'class', 'subjects', 'existingGrades', 'uspGrades', 'finalGrades', 'tempatCetak', 'tanggalCetak', 'nomorSkl', 'websiteLogo', 'websiteName', 'kepalaSekolah', 'average', 'isLulus', 'schoolProfile'));
        
        return $pdf->stream('SKL_' . $student->nis . '_' . $student->name . '.pdf');
    }

        public function cetakClassPdf(SchoolClass $class)
    {
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        if ($students->isEmpty()) {
            return redirect()->back()->withErrors(['Tidak ada siswa aktif di kelas ini untuk dicetak.']);
        }

        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();
        $tempatCetak = Setting::where('key', 'tempat_cetak')->first()?->value ?? 'Pacitan';
        $tanggalCetak = Setting::where('key', 'tanggal_cetak')->first()?->value ?? date('Y-m-d');
        
        $sklKode = Setting::where('key', 'skl_kode')->first()?->value ?? '400.3.11.1/';
        $sklKodeSekolah = Setting::where('key', 'skl_kode_sekolah')->first()?->value ?? ('/408.37.10.50/' . date('Y'));
        $sklNoUrutAwal = intval(Setting::where('key', 'skl_no_urut_awal')->first()?->value ?? 1);
        
        // Find all active students in the same grade and academic year, ordered by class name and student name
        $studentsInGrade = Student::join('school_classes', 'students.school_class_id', '=', 'school_classes.id')
            ->where('school_classes.grade', $class->grade)
            ->where('school_classes.academic_year', $class->academic_year)
            ->where('students.is_active', true)
            ->orderBy('school_classes.name', 'asc')
            ->orderBy('students.name', 'asc')
            ->select('students.*')
            ->get();

        $websiteLogo = Setting::where('key', 'website_logo')->first()?->value;
        $websiteName = Setting::where('key', 'website_name')->first()?->value ?? 'SIMSiswa';
        $schoolProfile = \Illuminate\Support\Facades\DB::table('school_profiles')->first();
        $kepalaSekolah = \Illuminate\Support\Facades\DB::table('teachers')->where('position', 'Kepala Sekolah')->first();

        $studentsData = [];
        
        foreach ($students as $student) {
            $existingGradesRaw = \DB::table('rapors')
                ->where('student_id', $student->id)
                ->get();

            // Fetch USP grades for this student
            $uspGradesRaw = \DB::table('usps')
                ->where('student_id', $student->id)
                ->get()
                ->keyBy('subject_id');

            // Fetch stored Final Grades for this student
            $finalGradesRaw = \DB::table('final_grades')
                ->where('student_id', $student->id)
                ->get()
                ->keyBy('subject_id');
                
            $existingGrades = [];
            $uspGrades = [];
            $finalGrades = [];
            $totalFinalGrade = 0;
            $countGrade = 0;
            
            foreach ($subjects as $subject) {
                $subjectGrades = $existingGradesRaw->where('subject_id', $subject->id);
                $total = $subjectGrades->sum('grade');
                $semesterCount = $subjectGrades->pluck('semester_id')->unique()->count();
                $avgRapor = $semesterCount > 0 ? ($total / $semesterCount) : 0;
                
                $existingGrades[$subject->id] = (object) ['grade' => $avgRapor];

                $uspValue = isset($uspGradesRaw[$subject->id]) ? floatval($uspGradesRaw[$subject->id]->grade) : 0;
                $uspGrades[$subject->id] = (object) ['grade' => $uspValue];

                // Read the stored final grade
                $finalGradeValue = isset($finalGradesRaw[$subject->id]) ? floatval($finalGradesRaw[$subject->id]->grade) : 0;
                $finalGrades[$subject->id] = (object) ['grade' => $finalGradeValue];

                $totalFinalGrade += $finalGradeValue;
                $countGrade++;
            }
            $average = $countGrade > 0 ? ($totalFinalGrade / $countGrade) : 0;
            
            $index = $studentsInGrade->pluck('id')->search($student->id);
            $sequenceNumber = str_pad(($index !== false ? $sklNoUrutAwal + $index : $sklNoUrutAwal), 3, '0', STR_PAD_LEFT);
            
            $nomorSkl = $sklKode . $sequenceNumber . $sklKodeSekolah;
            $isLulus = $average >= 65; 

            $studentsData[] = [
                'student' => $student,
                'existingGrades' => $existingGrades,
                'uspGrades' => $uspGrades,
                'finalGrades' => $finalGrades,
                'nomorSkl' => $nomorSkl,
                'average' => $average,
                'isLulus' => $isLulus
            ];
        }

        $pdf = Pdf::loadView('skl.pdf_all', compact('class', 'subjects', 'studentsData', 'tempatCetak', 'tanggalCetak', 'websiteLogo', 'websiteName', 'kepalaSekolah', 'schoolProfile'));
        
        return $pdf->stream('SKL_Massal_Kelas_' . $class->name . '.pdf');
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
     */
    public function nilaiAkhirShow(Request $request, SchoolClass $class)
    {
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();

        // Fetch existing final grades
        $existingGrades = \DB::table('final_grades')
            ->where('school_class_id', $class->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->student_id . '_' . $item->subject_id;
            });

        return view('skl.nilai_akhir_show', compact('class', 'students', 'subjects', 'existingGrades'));
    }

    public function nilaiAkhirStore(Request $request, SchoolClass $class)
    {
        $request->validate([
            'grades' => 'nullable|array'
        ]);

        $grades = $request->grades ?? [];

        foreach ($grades as $student_id => $studentSubjects) {
            foreach ($studentSubjects as $subject_id => $grade) {
                if ($grade !== null && $grade !== '') {
                    $grade = str_replace(',', '.', $grade);
                    \App\Models\FinalGrade::updateOrCreate(
                        [
                            'school_class_id' => $class->id,
                            'student_id' => $student_id,
                            'subject_id' => $subject_id,
                        ],
                        [
                            'grade' => round(floatval($grade), 2)
                        ]
                    );
                } else {
                    \App\Models\FinalGrade::where([
                        'school_class_id' => $class->id,
                        'student_id' => $student_id,
                        'subject_id' => $subject_id,
                    ])->delete();
                }
            }
        }

        return redirect()->route('nilai-akhir.show', ['class' => $class->id])
                         ->with('success', 'Data Nilai Akhir berhasil disimpan.');
    }

    public function nilaiAkhirExportExcel(Request $request, SchoolClass $class)
    {
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();
        
        $existingGrades = \DB::table('final_grades')
            ->where('school_class_id', $class->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->student_id . '_' . $item->subject_id;
            });

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Nilai Akhir');

        $headers = ['ID Siswa', 'Nama Siswa'];
        foreach ($subjects as $subject) {
            $headers[] = $subject->code ?: $subject->name;
        }
        $headers[] = 'Rata-Rata';
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($students as $student) {
            $rowData = [
                $student->id,
                $student->name
            ];
            $totalGrade = 0;
            $countGrade = 0;
            foreach ($subjects as $subject) {
                $key = $student->id . '_' . $subject->id;
                $grade = isset($existingGrades[$key]) ? $existingGrades[$key]->grade : null;
                $rowData[] = $grade !== null ? floatval($grade) : '';
                if ($grade !== null && $grade !== '') {
                    $totalGrade += floatval($grade);
                    $countGrade++;
                }
            }
            $rowData[] = $countGrade > 0 ? round($totalGrade / $countGrade, 2) : '';
            $sheet->fromArray($rowData, null, 'A' . $row);
            $row++;
        }

        foreach(range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Nilai_Akhir_Kelas_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $class->name) . '_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function nilaiAkhirImportTemplate(Request $request, SchoolClass $class)
    {
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Nilai Akhir');

        $headers = ['ID Siswa', 'Nama Siswa (Penting: Jangan Ubah ID)'];
        foreach ($subjects as $subject) {
            $headers[] = $subject->code ?: $subject->name;
        }
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($students as $student) {
            $rowData = [
                $student->id,
                $student->name
            ];
            $sheet->fromArray($rowData, null, 'A' . $row);
            $row++;
        }

        foreach(range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Template_Nilai_Akhir_Kelas_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $class->name) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function nilaiAkhirImportExcel(Request $request, SchoolClass $class)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            $header = array_shift($rows);
            if (!$header) {
                return redirect()->back()->withErrors(['Format file kosong atau tidak sesuai.']);
            }
            
            $subjects = Subject::get()->keyBy(function($item) {
                return trim($item->code ?: $item->name);
            });
            
            // Map header column index to subject ID
            $subjectColMap = [];
            foreach ($header as $index => $colName) {
                if ($index < 2) continue;
                $colName = trim($colName);
                if (isset($subjects[$colName])) {
                    $subjectColMap[$index] = $subjects[$colName]->id;
                }
            }

            $count = 0;
            foreach ($rows as $row) {
                $student_id = $row[0] ?? null;
                if (!$student_id) continue;

                foreach ($subjectColMap as $colIndex => $subject_id) {
                    $grade = $row[$colIndex] ?? null;
                    if ($grade !== null && $grade !== '') {
                        \App\Models\FinalGrade::updateOrCreate(
                            [
                                'school_class_id' => $class->id,
                                'student_id' => $student_id,
                                'subject_id' => $subject_id,
                            ],
                            [
                                'grade' => round(floatval($grade), 2)
                            ]
                        );
                        $count++;
                    }
                }
            }
            
            return redirect()->route('nilai-akhir.show', ['class' => $class->id])
                             ->with('success', 'Berhasil mengimport data Nilai Akhir.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Gagal mengimport file: ' . $e->getMessage()]);
        }
    }

    /**
     * Public page: Form cek kelulusan siswa
     */
    public function cekKelulusan()
    {
        $tanggalPengumuman = \App\Models\Setting::where('key', 'jadwal_pengumuman_tanggal')->first()?->value;
        $jamPengumuman = \App\Models\Setting::where('key', 'jadwal_pengumuman_jam')->first()?->value;
        
        $targetIso = null;
        if ($tanggalPengumuman && $jamPengumuman) {
            $targetIso = $tanggalPengumuman . 'T' . $jamPengumuman . ':00';
        }

        return view('skl.cek_kelulusan', compact('targetIso'));
    }

    /**
     * Public page: Verifikasi NIS/NISN + tanggal lahir, tampilkan hasil kelulusan
     */
    public function cekKelulusanCheck(Request $request)
    {
        $tanggalPengumuman = \App\Models\Setting::where('key', 'jadwal_pengumuman_tanggal')->first()?->value;
        $jamPengumuman = \App\Models\Setting::where('key', 'jadwal_pengumuman_jam')->first()?->value;
        
        if ($tanggalPengumuman && $jamPengumuman) {
            $announcementDateTime = \Carbon\Carbon::parse($tanggalPengumuman . ' ' . $jamPengumuman);
            if (\Carbon\Carbon::now()->lt($announcementDateTime)) {
                return redirect()->route('cek-kelulusan')
                    ->withErrors(['identifier' => 'Akses Kelulusan belum dibuka. Silakan kembali saat waktu pengumuman tiba.'])
                    ->withInput();
            }
        }

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

        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();

        // Fetch stored Final Grades for this student
        $finalGradesRaw = \DB::table('final_grades')
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('subject_id');

        $existingGrades = [];
        $totalFinalGrade = 0;
        $countGrade = 0;

        foreach ($subjects as $subject) {
            $finalGradeValue = isset($finalGradesRaw[$subject->id]) ? floatval($finalGradesRaw[$subject->id]->grade) : 0;
            $existingGrades[$subject->id] = (object) ['grade' => $finalGradeValue];
            $totalFinalGrade += $finalGradeValue;
            $countGrade++;
        }
        $average = $countGrade > 0 ? ($totalFinalGrade / $countGrade) : 0;

        $isLulus = $average >= 65;

        return view('skl.cek_kelulusan_result', compact('student', 'subjects', 'existingGrades', 'average', 'isLulus'));
    }
}
