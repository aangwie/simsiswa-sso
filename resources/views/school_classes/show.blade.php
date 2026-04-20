@extends('layouts.app')

@section('title', 'Detail Kelas - SIMSiswa')
@section('header', 'Daftar Siswa Kelas: ' . $schoolClass->name)

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Siswa Kelas {{ $schoolClass->name }}</h2>
            <p class="text-sm text-slate-500">Tingkat: {{ $schoolClass->grade }} | TA: {{ $schoolClass->academic_year }}</p>
        </div>
        <a href="{{ route('classes.index') }}" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">
            &larr; Kembali
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium">No</th>
                    <th class="px-6 py-4 font-medium">Nama Siswa</th>
                    <th class="px-6 py-4 font-medium">NIS</th>
                    <th class="px-6 py-4 font-medium">Gender</th>
                    <th class="px-6 py-4 font-medium">Status / Lulus</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($schoolClass->students as $index => $student)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 text-slate-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $student->name }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $student->nis ?? '-' }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        @if($student->status_lulus)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700 ml-1">
                            {{ ucfirst($student->status_lulus) }}
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                        Belum ada siswa di kelas ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
