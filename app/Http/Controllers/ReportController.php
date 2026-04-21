<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Rapor;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::withCount(['students' => function ($query) {
            $query->where('is_active', true);
        }])->paginate(10);
        return view('reports.index', compact('classes'));
    }

    public function show(Request $request, SchoolClass $class)
    {
        $semesters = Semester::orderBy('id', 'desc')->get();
        if ($semesters->isEmpty()) {
            return redirect()->route('semesters.index')->withErrors(['Silakan buat data Semester terlebih dahulu.']);
        }

        // Selected semester from query string or default to latest active
        $semester_id = $request->get('semester_id');
        if ($semester_id) {
            $currentSemester = Semester::find($semester_id);
        } else {
            $currentSemester = Semester::where('is_active', true)->first();
            if (!$currentSemester) {
                $currentSemester = $semesters->first();
            }
        }

        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $subjects = Subject::orderBy('code', 'asc')->get();

        // Fetch existing grades
        $existingGrades = \DB::table('rapors')
            ->where('semester_id', $currentSemester->id)
            ->where('school_class_id', $class->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->student_id . '_' . $item->subject_id;
            });

        return view('reports.show', compact('class', 'semesters', 'currentSemester', 'students', 'subjects', 'existingGrades'));
    }

    public function store(Request $request, SchoolClass $class)
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'grades' => 'nullable|array' // grades[student_id][subject_id] = value
        ]);

        $semester_id = $request->semester_id;
        $grades = $request->grades ?? [];

        $upsertData = [];
        $now = now();

        foreach ($grades as $student_id => $studentSubjects) {
            foreach ($studentSubjects as $subject_id => $grade) {
                if ($grade !== null && $grade !== '') {
                    Rapor::updateOrCreate(
                        [
                            'semester_id' => $semester_id,
                            'school_class_id' => $class->id,
                            'student_id' => $student_id,
                            'subject_id' => $subject_id,
                        ],
                        [
                            'grade' => $grade
                        ]
                    );
                }
            }
        }

        return redirect()->route('reports.show', ['class' => $class->id, 'semester_id' => $semester_id])
                         ->with('success', 'Data Rapor berhasil disimpan.');
    }

    public function exportExcel(Request $request, SchoolClass $class)
    {
        $semester_id = $request->get('semester_id');
        if (!$semester_id) {
            return redirect()->back()->withErrors(['Pilih semester terlebih dahulu untuk mengekspor data.']);
        }

        $semester = Semester::findOrFail($semester_id);
        
        $students = Student::where('school_class_id', $class->id)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
            
        $subjects = Subject::orderBy('code', 'asc')->get();
        
        $existingGrades = \DB::table('rapors')
            ->where('semester_id', $semester->id)
            ->where('school_class_id', $class->id)
            ->get()
            ->keyBy(function ($item) {
                return $item->student_id . '_' . $item->subject_id;
            });

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Rapor');

        $headers = ['ID Siswa', 'Nama Siswa'];
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
            foreach ($subjects as $subject) {
                $key = $student->id . '_' . $subject->id;
                $rowData[] = isset($existingGrades[$key]) ? $existingGrades[$key]->grade : '';
            }
            $sheet->fromArray($rowData, null, 'A' . $row);
            $row++;
        }

        foreach(range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Rapor_Kelas_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $class->name) . '_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $semester->name) . '_' . date('Ymd_His') . '.xlsx';
        
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
            
        $subjects = Subject::orderBy('code', 'asc')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Rapor');

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
        $fileName = 'Template_Rapor_Kelas_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $class->name) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function importExcel(Request $request, SchoolClass $class)
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $semester_id = $request->semester_id;
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
                if ($index < 2) continue; // Skip ID and Name
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
                        Rapor::updateOrCreate(
                            [
                                'semester_id' => $semester_id,
                                'school_class_id' => $class->id,
                                'student_id' => $student_id,
                                'subject_id' => $subject_id,
                            ],
                            [
                                'grade' => $grade
                            ]
                        );
                        $count++;
                    }
                }
            }
            
            return redirect()->route('reports.show', ['class' => $class->id, 'semester_id' => $semester_id])
                             ->with('success', 'Berhasil mengimport data rapor.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Gagal mengimport file: ' . $e->getMessage()]);
        }
    }
}
