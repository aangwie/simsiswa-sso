@extends('layouts.app')

@section('title', 'Pengaturan Website - SIMSiswa')
@section('header', 'Pengaturan Website')

@section('content')
    <div class="space-y-8 max-w-6xl mx-auto">
        <!-- Success Alert -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <ul class="text-sm font-medium text-red-700 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Settings Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-800">Identitas Website</h2>
            </div>

            <form action="{{ route('settings.website.update') }}" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-8">
                @csrf

                <div class="space-y-8">
                    <!-- Seksi: Informasi Website -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Informasi Website
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Website Name -->
                            <div class="space-y-2">
                                <label class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">Nama
                                    Website</label>
                                <input type="text" name="website_name" value="{{ $settings['website_name'] ?? 'SIMSiswa' }}"
                                    placeholder="Contoh: SIMSiswa SMP XYZ"
                                    class="w-full sm:w-3/4 rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2 text-sm">
                                <p class="text-[10px] text-slate-400 mt-1">Nama ini akan ditampilkan pada judul halaman,
                                    logo
                                    samping, dan di seluruh sistem.</p>
                            </div>

                            <!-- Website Logo -->
                            <div class="space-y-4">
                                <label class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">Logo
                                    Website /
                                    Favicon</label>

                                <div class="flex items-start gap-4 flex-col sm:flex-row">
                                    <div class="w-20 h-20 shrink-0 rounded-2xl bg-slate-50 border border-slate-200 shadow-inner flex items-center justify-center overflow-hidden"
                                        id="logo-preview-container">
                                        @if(!empty($settings['website_logo']))
                                            <img src="{{ $settings['website_logo'] }}" alt="Logo Preview"
                                                class="w-full h-full object-contain p-2" id="logo-preview-img">
                                        @else
                                            <svg class="h-8 w-8 text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" id="logo-preview-svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <img src="#" alt="Logo Preview" class="hidden w-full h-full object-contain p-2"
                                                id="logo-preview-img">
                                        @endif
                                    </div>

                                    <div class="flex-1 space-y-2">
                                        <input type="file" name="website_logo" id="website_logo" accept="image/*"
                                            class="w-full sm:w-3/4 text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors"
                                            onchange="previewLogo(this)">
                                        <p class="text-[10px] text-slate-400">Rekomendasi rasio 1:1, transparan (PNG).
                                            Ukuran
                                            maksimal 2MB. Ganti logo jika diperlukan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seksi: Profil Sekolah -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Profil Sekolah</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Nama Sekolah -->
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">Nama
                                    Sekolah</label>
                                <input type="text" name="nama_sekolah"
                                    value="{{ old('nama_sekolah', $schoolProfile->name ?? 'SMP Negeri 6 Sudimoro') }}"
                                    placeholder="Contoh: SMP Negeri 6 Sudimoro"
                                    class="w-full md:w-3/4 rounded-xl border-slate-200 bg-slate-50 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2 text-sm"
                                    readonly>
                            </div>

                            <!-- Alamat Sekolah -->
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">Alamat
                                    Sekolah</label>
                                <textarea name="alamat_sekolah" rows="2" placeholder="Masukkan alamat lengkap sekolah"
                                    class="w-full md:w-3/4 rounded-xl border-slate-200 bg-slate-50 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2 text-sm"
                                    readonly>{{ old('alamat_sekolah', $schoolProfile->address ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Seksi: Kepala Sekolah -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Kepala Sekolah</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Nama Kepala Sekolah -->
                            <div class="space-y-2">
                                <label class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">Nama
                                    Kepala
                                    Sekolah</label>
                                <input type="text" name="kepala_sekolah_name"
                                    value="{{ old('kepala_sekolah_name', $kepalaSekolah->name ?? '') }}"
                                    placeholder="Contoh: Budi Santoso, S.Pd"
                                    class="w-full sm:w-3/4 rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2 text-sm"
                                    readonly>
                            </div>

                            <!-- NIP Kepala Sekolah -->
                            <div class="space-y-2">
                                <label class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">NIP Kepala
                                    Sekolah</label>
                                <input type="text" name="kepala_sekolah_nip"
                                    value="{{ old('kepala_sekolah_nip', $kepalaSekolah->nip ?? '') }}"
                                    placeholder="Contoh: 19800101 200501 1 001"
                                    class="w-full sm:w-3/4 rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2 text-sm"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit"
                        class="px-8 py-2.5 bg-gradient-to-r from-purple-500 to-blue-500 text-white font-bold rounded-xl hover:from-purple-600 hover:to-blue-600 transition-all shadow-lg shadow-indigo-100 uppercase text-xs tracking-widest flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    var img = document.getElementById('logo-preview-img');
                    var svg = document.getElementById('logo-preview-svg');

                    img.src = e.target.result;
                    img.classList.remove('hidden');

                    if (svg) svg.classList.add('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection