@extends('layouts.app')

@section('title', 'Laporan BK - SIMSiswa')
@section('header', 'Laporan BK')

@section('content')
<div class="space-y-6">
    <!-- Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-800">Filter Laporan</h2>
            <p class="text-sm text-slate-500">Pilih filter untuk melihat laporan pelanggaran siswa.</p>
        </div>
        <form method="GET" class="p-6">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Siswa</label>
                    <select name="student_id" class="w-full rounded-xl border-slate-200 text-sm px-4 py-2">
                        <option value="">Semua Siswa</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Bulan</label>
                    <input type="month" name="bulan" value="{{ request('bulan') }}" class="w-full rounded-xl border-slate-200 text-sm px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Pelanggaran</label>
                    <select name="pelanggaran_id" class="w-full rounded-xl border-slate-200 text-sm px-4 py-2">
                        <option value="">Semua Jenis</option>
                        @foreach($pelanggarans as $p)
                            <option value="{{ $p->id }}" {{ request('pelanggaran_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_pelanggaran }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        Cari
                    </button>
                    @if($data->count() > 0)
                    <a href="{{ route('bk.laporan.pdf', request()->query()) }}" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors" title="Export PDF">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        PDF
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Result Table -->
    @if($data->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-800">Hasil Laporan</h2>
            <p class="text-sm text-slate-500">{{ $data->count() }} data ditemukan</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">#</th>
                        <th class="px-6 py-4 font-medium">Tanggal</th>
                        <th class="px-6 py-4 font-medium">Nama Siswa</th>
                        <th class="px-6 py-4 font-medium">Kelas</th>
                        <th class="px-6 py-4 font-medium">Pelanggaran</th>
                        <th class="px-6 py-4 font-medium text-center">Poin</th>
                        <th class="px-6 py-4 font-medium">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($data as $index => $d)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 text-slate-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-slate-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $d->student->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $d->student->schoolClass->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-800">{{ $d->pelanggaran->nama_pelanggaran ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-red-100 text-red-700">{{ $d->pelanggaran->poin ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $d->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-slate-50 font-bold">
                        <td colspan="5" class="px-6 py-4 text-right text-slate-700">Total Poin:</td>
                        <td class="px-6 py-4 text-center text-red-700">{{ $data->sum(fn($d) => $d->pelanggaran->poin ?? 0) }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @elseif(request()->hasAny(['student_id', 'bulan', 'pelanggaran_id']))
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
        <p class="text-slate-500 font-medium">Tidak ada data ditemukan dengan filter yang dipilih.</p>
    </div>
    @endif
</div>
@endsection
