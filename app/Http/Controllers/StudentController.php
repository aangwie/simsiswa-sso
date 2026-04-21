<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        
        $query = Student::with('schoolClass')
            ->select('students.*')
            ->leftJoin('school_classes', 'students.school_class_id', '=', 'school_classes.id');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('students.name', 'like', "%{$search}%")
                  ->orWhere('students.nis', 'like', "%{$search}%")
                  ->orWhere('students.nisn', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        if ($sort == 'class') {
            $query->orderBy('school_classes.name', $direction);
        } else {
            $query->orderBy('students.' . $sort, $direction);
        }

        $students = $query->paginate(10)->withQueryString();
        return view('students.index', compact('students', 'sort', 'direction'));
    }

    public function show(Student $student)
    {
        $student->load('schoolClass');
        return view('students.show', compact('student'));
    }

    public function exportPdf(Student $student)
    {
        $student->load('schoolClass');
        $pdf = Pdf::loadView('students.pdf', compact('student'));
        return $pdf->download('Data_Siswa_' . $student->name . '.pdf');
    }

    public function exportExcel()
    {
        $students = Student::with('schoolClass')->orderBy('name', 'asc')->get();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        $headers = ['No', 'Nama Siswa', 'NIS', 'NISN', 'Kelas', 'Tahun Masuk', 'Jenis Kelamin', 'Tanggal Lahir', 'Status Lulus', 'Nama Ayah', 'Nama Ibu', 'Alamat', 'Status Aktif'];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($students as $index => $student) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $student->name);
            $sheet->setCellValueExplicit('C' . $row, (string) $student->nis, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $row, (string) $student->nisn, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $row, $student->schoolClass ? $student->schoolClass->name : '');
            $sheet->setCellValue('F' . $row, $student->enrollment_year);
            $sheet->setCellValue('G' . $row, $student->gender == 'male' ? 'L' : 'P');
            $sheet->setCellValue('H' . $row, $student->tanggal_lahir);
            $sheet->setCellValue('I' . $row, $student->status_lulus);
            $sheet->setCellValue('J' . $row, $student->nama_ayah);
            $sheet->setCellValue('K' . $row, $student->nama_ibu);
            $sheet->setCellValue('L' . $row, $student->alamat);
            $sheet->setCellValue('M' . $row, $student->is_active ? 'Aktif' : 'Nonaktif');
            $row++;
        }

        foreach(range('A','M') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Data_Siswa_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function importTemplate()
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Siswa');

        $headers = ['Nama Siswa', 'Jenis Kelamin (L/P)', 'NIS', 'NISN', 'ID Kelas', 'Tahun Masuk', 'Tanggal Lahir (YYYY-MM-DD)', 'Nama Ayah', 'Nama Ibu', 'Alamat'];
        $sheet->fromArray($headers, null, 'A1');

        // Note: You can retrieve standard classes here if needed, but simple fallback to 1 is OK
        $example = ['Budi Santoso', 'L', '123456', '0012345678', '1', '2023', '2005-08-17', 'Ayah Budi', 'Ibu Budi', 'Jl. Merdeka No. 1'];
        $sheet->fromArray($example, null, 'A2');
        
        foreach(range('A','J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Template_Import_Siswa.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }

    public function importExcel(Request $request)
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // Remove header
            array_shift($rows);
            
            $count = 0;
            foreach ($rows as $row) {
                // Ignore empty rows (Nama Siswa)
                if (empty($row[0])) continue;

                Student::create([
                    'name' => $row[0],
                    'gender' => (strtoupper(trim($row[1] ?? '')) == 'P' || strtoupper(trim($row[1] ?? '')) == 'PEREMPUAN') ? 'female' : 'male',
                    'nis' => mb_substr((string) ($row[2] ?? ''), 0, 255) ?: null,
                    'nisn' => mb_substr((string) ($row[3] ?? ''), 0, 255) ?: null,
                    'school_class_id' => !empty($row[4]) ? $row[4] : 1, // fallback to class id 1
                    'enrollment_year' => $row[5] ?? date('Y'),
                    'tanggal_lahir' => !empty($row[6]) ? date('Y-m-d', strtotime($row[6])) : null,
                    'nama_ayah' => $row[7] ?? null,
                    'nama_ibu' => $row[8] ?? null,
                    'alamat' => $row[9] ?? null,
                    'is_active' => 1
                ]);
                $count++;
            }
            
            return redirect()->route('students.index')->with('success', $count . ' data siswa berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('students.index')->withErrors(['Gagal mengimport file: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        $classes = SchoolClass::all();
        return view('students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'nis' => 'nullable|string|max:255',
            'enrollment_year' => 'required|numeric',
            'nisn' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'status_lulus' => 'nullable|string|max:255',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        $classes = SchoolClass::all();
        return view('students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'nis' => 'nullable|string|max:255',
            'enrollment_year' => 'required|numeric',
            'nisn' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'status_lulus' => 'nullable|string|max:255',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}
