@extends('layouts.app')

@section('title', 'Data Poin Siswa - SIMSiswa')
@section('header', 'Data Poin Siswa')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Rekap Poin Pelanggaran Siswa</h2>
                <p class="text-sm text-slate-500">Total poin pelanggaran dan status per siswa.</p>
            </div>
            <button onclick="document.getElementById('addPoinModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 rounded-xl transition-all shadow-sm shadow-indigo-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Catat Pelanggaran
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 border-b border-green-100 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Search -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <form method="GET" class="relative">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..."
                        class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white shadow-sm text-sm focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">#</th>
                        <th class="px-6 py-4 font-medium">Nama Siswa</th>
                        <th class="px-6 py-4 font-medium">Kelas</th>
                        <th class="px-6 py-4 font-medium text-center">Jumlah Pelanggaran</th>
                        <th class="px-6 py-4 font-medium text-center">Total Poin</th>
                        <th class="px-6 py-4 font-medium text-center">Status</th>
                        <th class="px-6 py-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($poinSiswa as $index => $p)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 text-slate-400">{{ $poinSiswa->firstItem() + $index }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $p->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $p->kelas ?? '-' }}</td>
                        <td class="px-6 py-4 text-center text-slate-600">{{ $p->jumlah_pelanggaran }}x</td>
                        <td class="px-6 py-4 text-center font-bold text-slate-800">{{ $p->total_poin }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($p->total_poin <= 20)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-700">Aman</span>
                            @elseif($p->total_poin <= 50)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-yellow-100 text-yellow-700">Pembinaan</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-700">Berat</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('bk.riwayat', $p->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors inline-block" title="Detail Riwayat">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-slate-500">Belum ada data poin siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($poinSiswa->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $poinSiswa->links() }}</div>
        @endif
    </div>
</div>

<!-- Add Poin Modal -->
<div id="addPoinModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="document.getElementById('addPoinModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Catat Pelanggaran Siswa</h3>
            <button onclick="document.getElementById('addPoinModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-500"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>
        <form action="{{ route('bk.poin.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Siswa</label>
                    <select name="student_id" required class="w-full rounded-xl border-slate-200 text-sm px-4 py-2">
                        <option value="">Pilih Siswa</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Pelanggaran</label>
                    <select name="bk_pelanggaran_id" required class="w-full rounded-xl border-slate-200 text-sm px-4 py-2">
                        <option value="">Pilih Pelanggaran</option>
                        @foreach($pelanggarans as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_pelanggaran }} ({{ $p->poin }} poin)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-200 text-sm px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full rounded-xl border-slate-200 text-sm px-4 py-2" placeholder="Keterangan tambahan (opsional)"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addPoinModal').classList.add('hidden')" class="px-4 py-2 text-sm text-slate-700 bg-white border border-slate-300 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
