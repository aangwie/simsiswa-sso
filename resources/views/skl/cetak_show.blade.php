@extends('layouts.app')

@section('title', 'Cetak SKL - SIMSiswa')
@section('header', 'Daftar Siswa Cetak SKL')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col md:flex-row items-left justify-between gap-4">
            <!-- Title / Header -->
            <div class="text-left w-full md:w-auto flex-1">
                <h2 class="text-lg font-bold text-slate-800">
                    Siswa {{ $class->name }}
                </h2>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                <a href="{{ route('skl.cetak.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-indigo-600 rounded-xl transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="hidden md:inline">Kembali</span>
                </a>
            </div>
        </div>

        <!-- Alert Error -->
        @if($errors->any())
            <div class="bg-red-50 text-red-700 p-4 border border-red-100 flex items-start gap-3 rounded-2xl shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-medium">Terjadi kesalahan:</p>
                    <ul class="text-sm list-disc list-inside mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse border-b border-slate-200">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 text-sm border-y border-slate-200">
                            <th class="px-4 py-4 font-semibold border-r border-slate-200 w-16 text-center">No</th>
                            <th class="px-6 py-4 font-semibold border-r border-slate-200">NIS</th>
                            <th class="px-6 py-4 font-semibold border-r border-slate-200 w-full min-w-[200px]">Nama Siswa
                            </th>
                            <th
                                class="px-6 py-4 font-semibold border-r border-slate-200 text-center whitespace-nowrap bg-indigo-50 text-indigo-700">
                                Aksi Cetak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($students as $index => $student)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3 text-center border-r border-slate-200 text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-3 border-r border-slate-200 text-slate-600 font-mono text-sm">
                                    {{ $student->nis ?? '-' }}
                                </td>
                                <td class="px-6 py-3 border-r border-slate-200 font-medium text-slate-800">
                                    {{ $student->name }}
                                </td>
                                <td class="px-6 py-3 text-center border-r border-slate-200">
                                    <a href="{{ route('skl.cetak.pdf', ['student' => $student->id]) }}" target="_blank"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                            </path>
                                        </svg>
                                        Cetak PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500 bg-white">
                                    Belum ada siswa aktif di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection