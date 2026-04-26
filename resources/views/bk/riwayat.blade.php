@extends('layouts.app')

@section('title', 'Riwayat Siswa - SIMSiswa')
@section('header', 'Riwayat Siswa BK')

@section('content')
<div class="space-y-6">

    <!-- Biodata Siswa -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center shadow-lg shadow-indigo-200">
                    <span class="text-white font-bold text-xl">{{ substr($student->name, 0, 1) }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ $student->name }}</h2>
                    <p class="text-sm text-slate-500">{{ $student->schoolClass->name ?? '-' }} &bull; NIS: {{ $student->nis ?? '-' }} &bull; NISN: {{ $student->nisn ?? '-' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs text-slate-500">Total Poin</p>
                    <p class="text-2xl font-bold {{ $totalPoin <= 20 ? 'text-green-600' : ($totalPoin <= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $totalPoin }}</p>
                </div>
                @if($totalPoin <= 20)
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-green-100 text-green-700">Aman</span>
                @elseif($totalPoin <= 50)
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-yellow-100 text-yellow-700">Pembinaan</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-red-100 text-red-700">Berat</span>
                @endif
            </div>
        </div>
        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><span class="text-slate-400">Jenis Kelamin</span><p class="font-medium text-slate-800">{{ $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</p></div>
            <div><span class="text-slate-400">Tanggal Lahir</span><p class="font-medium text-slate-800">{{ $student->tanggal_lahir ? \Carbon\Carbon::parse($student->tanggal_lahir)->format('d/m/Y') : '-' }}</p></div>
            <div><span class="text-slate-400">Nama Ayah</span><p class="font-medium text-slate-800">{{ $student->nama_ayah ?? '-' }}</p></div>
            <div><span class="text-slate-400">Nama Ibu</span><p class="font-medium text-slate-800">{{ $student->nama_ibu ?? '-' }}</p></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Riwayat Pelanggaran -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Riwayat Pelanggaran</h3>
                <p class="text-sm text-slate-500">{{ $riwayatPelanggaran->count() }} pelanggaran tercatat</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="px-6 py-3 font-medium">Tanggal</th>
                            <th class="px-6 py-3 font-medium">Pelanggaran</th>
                            <th class="px-6 py-3 font-medium text-center">Poin</th>
                            <th class="px-6 py-3 font-medium">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($riwayatPelanggaran as $rp)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-3 text-slate-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($rp->tanggal)->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 font-medium text-slate-800">{{ $rp->pelanggaran->nama_pelanggaran ?? '-' }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-red-100 text-red-700">{{ $rp->pelanggaran->poin ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-3 text-slate-500">{{ $rp->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-6 text-center text-slate-400">Tidak ada riwayat pelanggaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Riwayat Konsultasi -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Riwayat Konsultasi</h3>
                <p class="text-sm text-slate-500">{{ $riwayatKonsultasi->count() }} konsultasi tercatat</p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($riwayatKonsultasi as $rk)
                <div class="p-4 hover:bg-slate-50/50">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-slate-800">{{ \Carbon\Carbon::parse($rk->tanggal_pengajuan)->format('d/m/Y') }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                                    {{ $rk->jenis_masalah == 'pribadi' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $rk->jenis_masalah == 'akademik' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $rk->jenis_masalah == 'sosial' ? 'bg-teal-100 text-teal-700' : '' }}
                                    {{ $rk->jenis_masalah == 'disiplin' ? 'bg-red-100 text-red-700' : '' }}
                                ">{{ ucfirst($rk->jenis_masalah) }}</span>
                            </div>
                            <p class="text-sm text-slate-600">{{ $rk->deskripsi ?? '-' }}</p>
                            @if($rk->jadwal)
                                <p class="text-xs text-slate-400 mt-1">Jadwal: {{ \Carbon\Carbon::parse($rk->jadwal->tanggal)->format('d/m/Y') }} {{ $rk->jadwal->jam }} - {{ $rk->jadwal->ruang }}</p>
                            @endif
                            @if($rk->solusi)
                                <div class="mt-2 p-2 bg-teal-50 rounded-lg border border-teal-100">
                                    <p class="text-xs font-medium text-teal-700">Solusi: {{ $rk->solusi->solusi ?? '-' }}</p>
                                    <p class="text-xs text-teal-600">Tindakan: {{ str_replace('_', ' ', ucfirst($rk->solusi->tindakan)) }} — {{ ucfirst($rk->solusi->status) }}</p>
                                </div>
                            @endif
                        </div>
                        <div>
                            @if($rk->status == 'pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                            @elseif($rk->status == 'dijadwalkan')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-700">Dijadwalkan</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-700">Selesai</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-slate-400">Tidak ada riwayat konsultasi.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="flex">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </div>
</div>
@endsection
