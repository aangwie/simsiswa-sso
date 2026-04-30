@extends('layouts.app')

@section('title', 'Nilai Akhir - SIMSiswa')
@section('header', 'Pilih Kelas')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Nilai Akhir</h1>
            <p class="text-sm text-slate-500 mt-1">NA = (Rata-rata Rapor × 60%) + (Nilai USP × 40%). Pilih kelas untuk melihat data.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($classes as $class)
        <a href="{{ route('nilai-akhir.show', $class->id) }}" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 transition-all hover:shadow-md hover:border-emerald-100 relative overflow-hidden flex flex-col h-full">
            <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
            
            <div class="relative z-10 flex-1">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-slate-800 mb-1">{{ $class->name }}</h3>
                <p class="text-sm text-slate-500 mb-4">{{ $class->grade }} - {{ $class->academic_year }}</p>
                
                <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-sm text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="font-medium">{{ $class->students_count }} Siswa Aktif</span>
                    </div>
                </div>
            </div>
            
            <div class="absolute bottom-4 right-4 text-emerald-400 opacity-0 transform translate-x-2 transition-all group-hover:opacity-100 group-hover:translate-x-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-slate-100">
                <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Belum ada data kelas</h3>
                <p class="text-slate-500 mb-6 max-w-md mx-auto">Anda perlu menambahkan data kelas terlebih dahulu di menu Manajemen -> Data Kelas.</p>
                <a href="{{ route('classes.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-emerald-600 hover:bg-emerald-700">
                    Kelola Data Kelas
                </a>
            </div>
        </div>
        @endforelse
    </div>

    @if($classes->hasPages())
    <div class="mt-6">
        {{ $classes->links() }}
    </div>
    @endif
</div>
@endsection
