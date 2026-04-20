@extends('layouts.app')

@section('title', 'Tambah Kelas - SIMSiswa')
@section('header', 'Tambah Kelas Baru')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl mx-auto">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Form Tambah Kelas</h2>
        </div>
        <a href="{{ route('classes.index') }}" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">
            &larr; Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="p-6 bg-red-50 border-b border-red-100">
        <ul class="list-disc list-inside text-sm text-red-600">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('classes.store') }}" method="POST" class="p-6 space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kelas</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border-slate-200 shadow-sm px-4 py-2" required placeholder="Contoh: 7A">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tingkat</label>
            <input type="text" name="grade" value="{{ old('grade') }}" class="w-full rounded-xl border-slate-200 shadow-sm px-4 py-2" required placeholder="Contoh: 7">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Ajaran</label>
            <input type="text" name="academic_year" value="{{ old('academic_year') }}" class="w-full rounded-xl border-slate-200 shadow-sm px-4 py-2" required placeholder="Contoh: 2025/2026">
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="is_active" class="ml-2 block text-sm text-slate-700">Status Aktif</label>
        </div>
        <div class="pt-4 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-blue-500 text-white rounded-xl hover:from-purple-600 hover:to-blue-600 shadow-sm transition-all">Simpan</button>
        </div>
    </form>
</div>
@endsection
