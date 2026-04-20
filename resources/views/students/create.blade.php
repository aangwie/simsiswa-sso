@extends('layouts.app')

@section('title', 'Tambah Siswa - SIMSiswa')
@section('header', 'Tambah Data Siswa')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Form Tambah Siswa</h2>
            <p class="text-sm text-slate-500">Silakan lengkapi form di bawah ini.</p>
        </div>
        <a href="{{ route('students.index') }}" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">
            &larr; Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="p-6 bg-red-50 border-b border-red-100">
        <ul class="list-disc list-inside text-sm text-red-600">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('students.store') }}" method="POST" class="p-6 space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2" required>
            </div>

            <!-- Kelas -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kelas</label>
                <select name="school_class_id" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- NIS -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">NIS</label>
                <input type="text" name="nis" value="{{ old('nis') }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2">
            </div>

            <!-- NISN -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">NISN</label>
                <input type="text" name="nisn" value="{{ old('nisn') }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2">
            </div>

            <!-- Gender -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin</label>
                <select name="gender" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2" required>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-Laki</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <!-- Tanggal Lahir -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2">
            </div>

            <!-- Enrollment Year -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Masuk</label>
                <input type="number" name="enrollment_year" value="{{ old('enrollment_year', date('Y')) }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2" required>
            </div>

            <!-- Status Lulus / Mutasi -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status Kelulusan</label>
                <select name="status_lulus" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2">
                    <option value="">Belum Lulus</option>
                    <option value="lulus" {{ old('status_lulus') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="mutasi" {{ old('status_lulus') == 'mutasi' ? 'selected' : '' }}>Mutasi</option>
                </select>
            </div>
            
            <!-- Nama Ayah -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Ayah</label>
                <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2">
            </div>

            <!-- Nama Ibu -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Ibu</label>
                <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2">
            </div>
        </div>

        <!-- Alamat -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
            <textarea name="alamat" rows="3" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-2">{{ old('alamat') }}</textarea>
        </div>

        <!-- Is Active -->
        <div class="flex items-center">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="is_active" class="ml-2 block text-sm text-slate-700">Status Aktif</label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-blue-500 text-white font-medium rounded-xl hover:from-purple-600 hover:to-blue-600 transition-all shadow-sm shadow-indigo-200">
                Simpan Data
            </button>
        </div>
    </form>
</div>
@endsection
