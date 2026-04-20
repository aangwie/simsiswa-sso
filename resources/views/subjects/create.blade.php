@extends('layouts.app')

@section('title', 'Tambah Mapel - SIMSiswa')
@section('header', 'Tambah Mata Pelajaran')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl mx-auto">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Form Mata Pelajaran</h2>
        </div>
        <a href="{{ route('subjects.index') }}" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">
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

    <form action="{{ route('subjects.store') }}" method="POST" class="p-6 space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kode Mapel</label>
            <input type="text" name="code" value="{{ old('code') }}" class="w-full rounded-xl border-slate-200 shadow-sm px-4 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Mapel</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border-slate-200 shadow-sm px-4 py-2" required>
        </div>
        <div class="pt-4 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-blue-500 text-white font-medium rounded-xl hover:from-purple-600 hover:to-blue-600 transition-all shadow-sm">Simpan</button>
        </div>
    </form>
</div>
@endsection
