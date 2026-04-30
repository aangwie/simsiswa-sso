<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Usp;
use Illuminate\Http\Request;

class UspController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::withCount(['students' => function ($query) {
            $query->where('is_active', true);
        }])->paginate(10);
        return view('usp.index', compact('classes'));
    }

    public function show(Request $request, SchoolClass $class)
    {
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();

        // Fetch existing USP grades
        $existingGrades = \DB::table('usps')
            ->where('school_class_id', $class->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->student_id . '_' . $item->subject_id;
            });

        return view('usp.show', compact('class', 'students', 'subjects', 'existingGrades'));
    }

    public function store(Request $request, SchoolClass $class)
    {
        $request->validate([
            'grades' => 'nullable|array'
        ]);

        $grades = $request->grades ?? [];

        foreach ($grades as $student_id => $studentSubjects) {
            foreach ($studentSubjects as $subject_id => $grade) {
                if ($grade !== null && $grade !== '') {
                    $grade = str_replace(',', '.', $grade);
                    Usp::updateOrCreate(
                        [
                            'school_class_id' => $class->id,
                            'student_id' => $student_id,
                            'subject_id' => $subject_id,
                        ],
                        [
                            'grade' => round(floatval($grade), 2)
                        ]
                    );
                }
            }
        }

        return redirect()->route('usp.show', ['class' => $class->id])
                         ->with('success', 'Data USP berhasil disimpan.');
    }

    public function exportExcel(Request $request, SchoolClass $class)
    {
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();
        
        $existingGrades = \DB::table('usps')
            ->where('school_class_id', $class->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->student_id . '_' . $item->subject_id;
            });

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data USP');

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
            $totalUsp = 0;
            $countUsp = 0;
            foreach ($subjects as $subject) {
                $key = $student->id . '_' . $subject->id;
                $grade = isset($existingGrades[$key]) ? $existingGrades[$key]->grade : null;
                $rowData[] = $grade !== null ? floatval($grade) : '';
                if ($grade !== null && $grade !== '') {
                    $totalUsp += floatval($grade);
                    $countUsp++;
                }
            }
            $rowData[] = $countUsp > 0 ? round($totalUsp / $countUsp, 2) : '';
            $sheet->fromArray($rowData, null, 'A' . $row);
            $row++;
        }

        foreach(range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'USP_Kelas_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $class->name) . '_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function importTemplate(Request $request, SchoolClass $class)
    {
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $subjects = Subject::whereNotNull('order')->orderBy('order', 'asc')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template USP');

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
        $fileName = 'Template_USP_Kelas_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $class->name) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function importExcel(Request $request, SchoolClass $class)
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
                        Usp::updateOrCreate(
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
            
            return redirect()->route('usp.show', ['class' => $class->id])
                             ->with('success', 'Berhasil mengimport data USP.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Gagal mengimport file: ' . $e->getMessage()]);
        }
    }
}
