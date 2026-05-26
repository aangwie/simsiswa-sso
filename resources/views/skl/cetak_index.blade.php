@extends('layouts.app')

@section('title', 'Cetak SKL - SIMSiswa')
@section('header', 'Pengaturan & Pilih Kelas')

@section('content')
<div class="space-y-6">
    <!-- Pengaturan Cetak -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h2 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">Pengaturan Cetak SKL</h2>
        <form action="{{ route('skl.cetak.settings') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 tracking-wide mb-2">Tempat Cetak</label>
                <input type="text" name="tempat_cetak" value="{{ $tempatCetak }}" placeholder="Contoh: Pacitan" class="w-full rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 py-2 px-3 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 tracking-wide mb-2">Tanggal Cetak</label>
                <input type="date" name="tanggal_cetak" value="{{ $tanggalCetak }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 py-2 px-3 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 tracking-wide mb-2">Nomor Urut Awal</label>
                <input type="number" name="skl_no_urut_awal" value="{{ $sklNoUrutAwal }}" placeholder="Contoh: 56" min="1" class="w-full rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 py-2 px-3 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 tracking-wide mb-2">Kode Nomor SKL (Bagian Depan)</label>
                <input type="text" name="skl_kode" value="{{ $sklKode }}" placeholder="Contoh: 400.3.11.1/" class="w-full rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 py-2 px-3 text-sm" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 tracking-wide mb-2">Kode Sekolah SKL (Bagian Belakang)</label>
                <input type="text" name="skl_kode_sekolah" value="{{ $sklKodeSekolah }}" placeholder="Contoh: /408.37.10.50/2026" class="w-full rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 py-2 px-3 text-sm" required>
            </div>
            <div class="md:col-span-3 bg-indigo-50/50 p-4 rounded-xl border border-indigo-100/50 text-xs text-indigo-700">
                <p class="font-semibold mb-1">💡 Preview Format Nomor SKL:</p>
                <p class="font-mono text-sm mt-1">
                    <span class="bg-indigo-100/80 px-2 py-0.5 rounded text-indigo-800 font-bold" id="preview-kode">{{ $sklKode }}</span><span class="bg-amber-100 px-2 py-0.5 rounded text-amber-800 font-bold" id="preview-no-urut">{{ str_pad($sklNoUrutAwal, 3, '0', STR_PAD_LEFT) }}</span><span class="bg-emerald-100 px-2 py-0.5 rounded text-emerald-800 font-bold" id="preview-kode-sekolah">{{ $sklKodeSekolah }}</span>
                </p>
                <p class="mt-2 text-slate-500 text-[11px]">Keterangan: Siswa pertama dalam urutan global akan mendapat nomor urut (<span class="text-amber-600 font-bold" id="preview-desc-no-urut">{{ str_pad($sklNoUrutAwal, 3, '0', STR_PAD_LEFT) }}</span>), lalu otomatis bertambah seiring jumlah siswa (misal: {{ str_pad($sklNoUrutAwal, 3, '0', STR_PAD_LEFT) }}, {{ str_pad($sklNoUrutAwal + 1, 3, '0', STR_PAD_LEFT) }}, dst).</p>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const inputKode = document.querySelector('input[name="skl_kode"]');
                    const inputKodeSekolah = document.querySelector('input[name="skl_kode_sekolah"]');
                    const inputNoUrutAwal = document.querySelector('input[name="skl_no_urut_awal"]');
                    const previewKode = document.getElementById('preview-kode');
                    const previewNoUrut = document.getElementById('preview-no-urut');
                    const previewDescNoUrut = document.getElementById('preview-desc-no-urut');
                    const previewKodeSekolah = document.getElementById('preview-kode-sekolah');
            
                    function updatePreview() {
                        const rawNum = parseInt(inputNoUrutAwal.value) || 1;
                        const paddedNum = String(rawNum).padStart(3, '0');
                        
                        previewKode.textContent = inputKode.value || '';
                        previewNoUrut.textContent = paddedNum;
                        previewDescNoUrut.textContent = paddedNum;
                        previewKodeSekolah.textContent = inputKodeSekolah.value || '';
                    }
            
                    if (inputKode && inputKodeSekolah && inputNoUrutAwal && previewKode && previewNoUrut && previewDescNoUrut && previewKodeSekolah) {
                        inputKode.addEventListener('input', updatePreview);
                        inputKodeSekolah.addEventListener('input', updatePreview);
                        inputNoUrutAwal.addEventListener('input', updatePreview);
                    }
                });
            </script>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all text-sm shadow w-full sm:w-auto">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    @if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 rounded-2xl border border-green-100 flex items-center gap-3 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif
    
    @if($errors->any())
    <div class="bg-red-50 text-red-700 p-4 border border-red-100 flex items-start gap-3 rounded-2xl shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <p class="text-sm font-medium">Informasi Penting:</p>
            <ul class="text-sm list-disc list-inside mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($classes as $class)
        <a href="{{ route('skl.cetak.show', $class->id) }}" class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-100 transition-all hover:shadow-md hover:border-indigo-100 relative overflow-hidden flex flex-col h-full">
            <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
            
            <div class="relative z-10 flex-1">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
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
            
            <div class="absolute bottom-4 right-4 text-indigo-400 opacity-0 transform translate-x-2 transition-all group-hover:opacity-100 group-hover:translate-x-0">
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
                <a href="{{ route('classes.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700">
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
