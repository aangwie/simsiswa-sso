<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Rapor;
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
            
        $aggregatedGrades = [];
        foreach ($existingGradesRaw as $grade) {
            $key = $grade->student_id . '_' . $grade->subject_id;
            if (!isset($aggregatedGrades[$key])) {
                $aggregatedGrades[$key] = ['total' => 0, 'count' => 0];
            }
            $aggregatedGrades[$key]['total'] += floatval($grade->grade);
            $aggregatedGrades[$key]['count']++;
        }
        
        $existingGrades = [];
        foreach ($aggregatedGrades as $key => $data) {
            $existingGrades[$key] = (object) ['grade' => $data['count'] > 0 ? ($data['total'] / $data['count']) : 0];
        }

        return view('skl.show', compact('class', 'students', 'subjects', 'existingGrades'));
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
            
        $aggregatedGrades = [];
        foreach ($existingGradesRaw as $grade) {
            $key = $grade->student_id . '_' . $grade->subject_id;
            if (!isset($aggregatedGrades[$key])) {
                $aggregatedGrades[$key] = ['total' => 0, 'count' => 0];
            }
            $aggregatedGrades[$key]['total'] += floatval($grade->grade);
            $aggregatedGrades[$key]['count']++;
        }
        
        $existingGrades = [];
        foreach ($aggregatedGrades as $key => $data) {
            $existingGrades[$key] = (object) ['grade' => $data['count'] > 0 ? ($data['total'] / $data['count']) : 0];
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data SKL Kumulatif');

        $headers = ['NO. URUT', 'NO. INDUK', 'NAMA PESERTA USP', 'L/P'];
        foreach ($subjects as $subject) {
            $headers[] = $subject->name;
        }
        $headers[] = 'RATA-RATA NILAI RAPOR SEMUA MAPEL';
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
            $countGrade = 0;

            foreach ($subjects as $subject) {
                $key = $student->id . '_' . $subject->id;
                $grade = isset($existingGrades[$key]) ? floatval($existingGrades[$key]->grade) : 0;
                $rowData[] = $grade;
                $totalGrade += $grade;
                $countGrade++;
            }

            $average = $countGrade > 0 ? ($totalGrade / $countGrade) : 0;
            
            // Format average to 2 decimal places using round
            $rowData[] = round($average, 2);

            // Behavioral grade
            $rowData[] = 'B';

            // Pass status
            if ($average >= $min_grade) {
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
            
        $existingGrades = [];
        $totalGrade = 0;
        $countGrade = 0;
        
        foreach ($subjects as $subject) {
            $subjectGrades = $existingGradesRaw->where('subject_id', $subject->id);
            $count = $subjectGrades->count();
            if ($count > 0) {
                $avg = $subjectGrades->sum('grade') / $count;
            } else {
                $avg = 0;
            }
            
            $existingGrades[$subject->id] = (object) ['grade' => $avg];
            
            $totalGrade += $avg;
            $countGrade++;
        }
        $average = $countGrade > 0 ? ($totalGrade / $countGrade) : 0;
            
        $tempatCetak = Setting::where('key', 'tempat_cetak')->first()->value ?? 'Pacitan';
        $tanggalCetak = Setting::where('key', 'tanggal_cetak')->first()->value ?? date('Y-m-d');
        $nomorSkl = Setting::where('key', 'nomor_skl')->first()->value ?? '400.3.11.1/059/408.37.10.50/' . date('Y');
        
        $websiteLogo = Setting::where('key', 'website_logo')->first()?->value;
        $websiteName = Setting::where('key', 'website_name')->first()?->value ?? 'SIMSiswa';
        $schoolProfile = \Illuminate\Support\Facades\DB::table('school_profiles')->first();

        $kepalaSekolah = \Illuminate\Support\Facades\DB::table('teachers')->where('position', 'Kepala Sekolah')->first();
        
        // Pass threshold uses same constant assumption as export does or basic 65
        $isLulus = $average >= 65; 
        
        $pdf = Pdf::loadView('skl.pdf', compact('student', 'class', 'subjects', 'existingGrades', 'tempatCetak', 'tanggalCetak', 'nomorSkl', 'websiteLogo', 'websiteName', 'kepalaSekolah', 'average', 'isLulus', 'schoolProfile'));
        
        return $pdf->stream('SKL_' . $student->nis . '_' . $student->name . '.pdf');
    }
}
