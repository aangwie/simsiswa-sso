<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::orderBy('id', 'desc')->paginate(10);
        return view('semesters.index', compact('semesters'));
    }

    public function create()
    {
        return view('semesters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Semester::create($validated);

        return redirect()->route('semesters.index')->with('success', 'Semester berhasil ditambahkan.');
    }

    public function edit(Semester $semester)
    {
        return view('semesters.edit', compact('semester'));
    }

    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $semester->update($validated);

        return redirect()->route('semesters.index')->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();
        return redirect()->route('semesters.index')->with('success', 'Semester berhasil dihapus.');
    }
}
