<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::withCount('students')->paginate(10);
        return view('school_classes.index', compact('classes'));
    }

    public function show($id)
    {
        $schoolClass = SchoolClass::with(['students' => function($query) {
            $query->where('is_active', true);
        }])->findOrFail($id);
        
        return view('school_classes.show', compact('schoolClass'));
    }

    public function create()
    {
        return view('school_classes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'academic_year' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        SchoolClass::create($validated);

        return redirect()->route('classes.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(SchoolClass $class)
    {
        return view('school_classes.edit', compact('class'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'academic_year' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $class->update($validated);

        return redirect()->route('classes.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();
        return redirect()->route('classes.index')->with('success', 'Data kelas berhasil dihapus.');
    }
}
