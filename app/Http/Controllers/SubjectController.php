<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderBy('category', 'asc')->orderBy('order', 'asc')->paginate(10);
        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        return view('subjects.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:subjects,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Kelompok A,Kelompok B',
            'order' => 'required|integer|min:1|max:16',
        ]);

        Subject::create($validated);

        return redirect()->route('subjects.index')->with('success', 'Data mata pelajaran berhasil ditambahkan.');
    }

    public function edit(Subject $subject)
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Kelompok A,Kelompok B',
            'order' => 'required|integer|min:1|max:16',
        ]);

        $subject->update($validated);

        return redirect()->route('subjects.index')->with('success', 'Data mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        if (auth()->user()->role === 'teacher') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Data mata pelajaran berhasil dihapus.');
    }
}
