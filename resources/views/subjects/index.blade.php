@extends('layouts.app')

@section('title', 'Mata Pelajaran - SIMSiswa')
@section('header', 'Data Mata Pelajaran')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Daftar Mata Pelajaran</h2>
            <p class="text-sm text-slate-500">Kelola master data mata pelajaran sekolah.</p>
        </div>
        @if(auth()->user()->role !== 'teacher')
        <div>
            <a href="{{ route('subjects.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 rounded-xl transition-all shadow-sm shadow-indigo-200">
                Tambah Mata Pelajaran
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 border-b border-green-100 flex items-center gap-3">
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium">No</th>
                    <th class="px-6 py-4 font-medium">Kode Mapel</th>
                    <th class="px-6 py-4 font-medium">Nama Mata Pelajaran</th>
                    <th class="px-6 py-4 font-medium text-center">Kategori</th>
                    <th class="px-6 py-4 font-medium text-center">Urutan</th>
                    <th class="px-6 py-4 font-medium text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($subjects as $index => $subject)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 text-slate-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $subject->code }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $subject->name }}</td>
                    <td class="px-6 py-4 text-center text-slate-600">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                            {{ $subject->category ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 text-xs font-bold">
                            {{ $subject->order ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if(auth()->user()->role !== 'teacher')
                            <a href="{{ route('subjects.edit', $subject->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-slate-400 italic">Read Only</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada data mata pelajaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($subjects->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $subjects->links() }}
    </div>
    @endif
</div>
@endsection
