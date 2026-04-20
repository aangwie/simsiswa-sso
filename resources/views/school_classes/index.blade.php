@extends('layouts.app')

@section('title', 'Manajemen Kelas - SIMSiswa')
@section('header', 'Data Kelas')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Daftar Kelas</h2>
            <p class="text-sm text-slate-500">Pilih kelas untuk melihat daftar siswanya.</p>
        </div>
        <div>
            <a href="{{ route('classes.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 rounded-xl transition-all shadow-sm shadow-indigo-200">
                Tambah Kelas
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 border-b border-green-100 flex items-center gap-3">
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
        @forelse($classes as $c)
        <a href="{{ route('classes.show', $c->id) }}" class="block bg-slate-50 border border-slate-200 rounded-2xl p-6 hover:shadow-md hover:border-indigo-300 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="text-sm font-medium px-2 py-1 bg-white border border-slate-200 rounded-lg text-slate-500">{{ $c->students_count }} Siswa</span>
            </div>
            <h3 class="text-xl font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $c->name }}</h3>
            <p class="text-sm text-slate-500 mt-1">Tingkat: {{ $c->grade }} • Tahun Ajaran: {{ $c->academic_year }}</p>
            
            <div class="mt-4 pt-4 border-t border-slate-200 flex justify-between items-center">
                <span class="text-sm text-indigo-600 font-medium">Lihat Siswa &rarr;</span>
                <form action="{{ route('classes.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus kelas ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                </form>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-10 text-slate-500">Belum ada data kelas.</div>
        @endforelse
    </div>
</div>
@endsection
