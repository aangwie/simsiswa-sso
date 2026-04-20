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

    public function create()
    {
        $classes = SchoolClass::all();
        return view('students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'nis' => 'nullable|string|max:255',
            'enrollment_year' => 'required|numeric',
            'nisn' => 'nullable|string|max:255',
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
        $classes = SchoolClass::all();
        return view('students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'nis' => 'nullable|string|max:255',
            'enrollment_year' => 'required|numeric',
            'nisn' => 'nullable|string|max:255',
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
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}
