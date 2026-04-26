@extends('layouts.app')

@section('title', 'Dashboard BK - SIMSiswa')
@section('header', 'Dashboard BK')

@section('content')
<div class="space-y-6">

    <!-- Metric Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
        <!-- Siswa Bermasalah -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group relative overflow-hidden">
            <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-red-100 to-red-50 rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center mb-3 md:mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-slate-500 text-xs md:text-sm font-medium">Siswa Bermasalah</h3>
                <div class="mt-1 md:mt-2">
                    <span class="text-xl md:text-3xl font-bold text-slate-800">{{ number_format($siswaBermasalah) }}</span>
                    <span class="text-xs text-slate-400 block">Poin > 20</span>
                </div>
            </div>
        </div>

        <!-- Konsultasi Hari Ini -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group relative overflow-hidden">
            <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-50 rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-3 md:mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-slate-500 text-xs md:text-sm font-medium">Konsultasi Hari Ini</h3>
                <div class="mt-1 md:mt-2">
                    <span class="text-xl md:text-3xl font-bold text-slate-800">{{ number_format($konsultasiHariIni) }}</span>
                </div>
            </div>
        </div>

        <!-- Total Konsultasi -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group relative overflow-hidden">
            <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3 md:mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                </div>
                <h3 class="text-slate-500 text-xs md:text-sm font-medium">Total Konsultasi</h3>
                <div class="mt-1 md:mt-2">
                    <span class="text-xl md:text-3xl font-bold text-slate-800">{{ number_format($totalKonsultasi) }}</span>
                </div>
            </div>
        </div>

        <!-- Total Pelanggaran -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group relative overflow-hidden">
            <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-orange-100 to-orange-50 rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center mb-3 md:mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-slate-500 text-xs md:text-sm font-medium">Pelanggaran Tercatat</h3>
                <div class="mt-1 md:mt-2">
                    <span class="text-xl md:text-3xl font-bold text-slate-800">{{ number_format($totalPelanggaran) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 Pelanggaran Terbanyak -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-800">Top 5 Pelanggaran Terbanyak</h2>
                <p class="text-sm text-slate-500">Jenis pelanggaran yang paling sering terjadi.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="px-6 py-3 font-medium">#</th>
                            <th class="px-6 py-3 font-medium">Pelanggaran</th>
                            <th class="px-6 py-3 font-medium text-center">Poin</th>
                            <th class="px-6 py-3 font-medium text-center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($topPelanggaran as $index => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-3 text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-6 py-3 font-medium text-slate-800">{{ $item->nama_pelanggaran }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-700">{{ $item->poin }}</span>
                            </td>
                            <td class="px-6 py-3 text-center font-bold text-slate-700">{{ $item->jumlah }}x</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada data pelanggaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Siswa dengan Poin Tertinggi -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-800">Siswa Poin Tertinggi</h2>
                <p class="text-sm text-slate-500">Siswa yang perlu perhatian khusus.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="px-6 py-3 font-medium">#</th>
                            <th class="px-6 py-3 font-medium">Nama Siswa</th>
                            <th class="px-6 py-3 font-medium">Kelas</th>
                            <th class="px-6 py-3 font-medium text-center">Total Poin</th>
                            <th class="px-6 py-3 font-medium text-center">Status</th>
                            <th class="px-6 py-3 font-medium text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($topSiswa as $index => $siswa)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-3 text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-6 py-3 font-medium text-slate-800">{{ $siswa->name }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $siswa->kelas ?? '-' }}</td>
                            <td class="px-6 py-3 text-center font-bold text-slate-800">{{ $siswa->total_poin }}</td>
                            <td class="px-6 py-3 text-center">
                                @if($siswa->total_poin <= 20)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-700">Aman</span>
                                @elseif($siswa->total_poin <= 50)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-yellow-100 text-yellow-700">Pembinaan</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-700">Berat</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('bk.riwayat', $siswa->id) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data poin siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
