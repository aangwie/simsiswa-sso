@extends('layouts.app')

@section('title', 'Konsultasi BK - SIMSiswa')
@section('header', 'Konsultasi BK')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Data Konsultasi</h2>
                <p class="text-sm text-slate-500">Kelola pengajuan dan riwayat konsultasi siswa.</p>
            </div>
            <button onclick="document.getElementById('addKonsultasiModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 rounded-xl transition-all shadow-sm shadow-indigo-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Ajukan Konsultasi
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 border-b border-green-100 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Filter -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Siswa</label>
                    <select name="student_id" class="rounded-xl border-slate-200 text-sm px-3 py-2">
                        <option value="">Semua</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="rounded-xl border-slate-200 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="rounded-xl border-slate-200 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                    <select name="status" class="rounded-xl border-slate-200 text-sm px-3 py-2">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="dijadwalkan" {{ request('status') == 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">Filter</button>
                <a href="{{ route('bk.konsultasi.index') }}" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50">Reset</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Tanggal</th>
                        <th class="px-6 py-4 font-medium">Siswa</th>
                        <th class="px-6 py-4 font-medium">Jenis</th>
                        <th class="px-6 py-4 font-medium">Deskripsi</th>
                        <th class="px-6 py-4 font-medium text-center">Status</th>
                        <th class="px-6 py-4 font-medium text-center">Jadwal</th>
                        <th class="px-6 py-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($konsultasi as $k)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 text-slate-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($k->tanggal_pengajuan)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $k->student->name ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $k->student->schoolClass->name ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                                {{ $k->jenis_masalah == 'pribadi' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $k->jenis_masalah == 'akademik' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $k->jenis_masalah == 'sosial' ? 'bg-teal-100 text-teal-700' : '' }}
                                {{ $k->jenis_masalah == 'disiplin' ? 'bg-red-100 text-red-700' : '' }}
                            ">{{ ucfirst($k->jenis_masalah) }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 max-w-xs truncate">{{ $k->deskripsi ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($k->status == 'pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                            @elseif($k->status == 'dijadwalkan')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-700">Dijadwalkan</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-700">Selesai</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-xs text-slate-500">
                            @if($k->jadwal)
                                {{ \Carbon\Carbon::parse($k->jadwal->tanggal)->format('d/m/Y') }} {{ $k->jadwal->jam }}<br>
                                <span class="text-slate-400">{{ $k->jadwal->ruang }}</span>
                            @else
                                <button onclick="showJadwalModal({{ $k->id }})" class="text-indigo-600 hover:underline text-xs font-medium">+ Jadwalkan</button>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="showSolusiModal({{ $k->id }}, '{{ addslashes($k->solusi->solusi ?? '') }}', '{{ $k->solusi->tindakan ?? '' }}', '{{ $k->solusi->status ?? 'pending' }}')"
                                    class="p-1.5 text-teal-600 hover:bg-teal-50 rounded-lg transition-colors" title="Solusi">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                                </button>
                                <a href="{{ route('bk.riwayat', $k->student_id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Riwayat">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                <form action="{{ route('bk.konsultasi.destroy', $k->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-slate-500">Belum ada data konsultasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($konsultasi->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $konsultasi->links() }}</div>
        @endif
    </div>
</div>

<!-- Add Konsultasi Modal -->
<div id="addKonsultasiModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="document.getElementById('addKonsultasiModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Ajukan Konsultasi</h3>
            <button onclick="document.getElementById('addKonsultasiModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-500"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>
        <form action="{{ route('bk.konsultasi.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Siswa</label>
                    <select name="student_id" required class="w-full rounded-xl border-slate-200 text-sm px-4 py-2">
                        <option value="">Pilih Siswa</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->schoolClass->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Masalah</label>
                    <select name="jenis_masalah" required class="w-full rounded-xl border-slate-200 text-sm px-4 py-2">
                        <option value="pribadi">Pribadi</option>
                        <option value="akademik">Akademik</option>
                        <option value="sosial">Sosial</option>
                        <option value="disiplin">Disiplin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full rounded-xl border-slate-200 text-sm px-4 py-2" placeholder="Jelaskan masalah..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addKonsultasiModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">Ajukan</button>
            </div>
        </form>
    </div>
</div>

<!-- Jadwal Modal -->
<div id="jadwalModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="document.getElementById('jadwalModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Jadwalkan Konsultasi</h3>
        </div>
        <form action="{{ route('bk.jadwal.store') }}" method="POST">
            @csrf
            <input type="hidden" name="bk_konsultasi_id" id="jadwal_konsultasi_id">
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" required class="w-full rounded-xl border-slate-200 text-sm px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jam</label>
                        <input type="time" name="jam" required class="w-full rounded-xl border-slate-200 text-sm px-4 py-2">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Guru BK</label>
                    <input type="text" name="guru_bk" class="w-full rounded-xl border-slate-200 text-sm px-4 py-2" placeholder="Nama Guru BK">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ruang</label>
                    <input type="text" name="ruang" class="w-full rounded-xl border-slate-200 text-sm px-4 py-2" placeholder="Ruang BK">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                    <textarea name="catatan" rows="2" class="w-full rounded-xl border-slate-200 text-sm px-4 py-2"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('jadwalModal').classList.add('hidden')" class="px-4 py-2 text-sm text-slate-700 bg-white border border-slate-300 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<!-- Solusi Modal -->
<div id="solusiModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="document.getElementById('solusiModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Solusi & Tindak Lanjut</h3>
        </div>
        <form action="{{ route('bk.solusi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="bk_konsultasi_id" id="solusi_konsultasi_id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Solusi</label>
                    <textarea name="solusi" id="solusi_text" rows="3" class="w-full rounded-xl border-slate-200 text-sm px-4 py-2" placeholder="Tuliskan solusi..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tindakan</label>
                    <select name="tindakan" id="solusi_tindakan" required class="w-full rounded-xl border-slate-200 text-sm px-4 py-2">
                        <option value="konseling_lanjutan">Konseling Lanjutan</option>
                        <option value="panggilan_orang_tua">Panggilan Orang Tua</option>
                        <option value="surat_peringatan">Surat Peringatan</option>
                    </select>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('solusiModal').classList.add('hidden')" class="px-4 py-2 text-sm text-slate-700 bg-white border border-slate-300 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-xl hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function showJadwalModal(konsultasiId) {
    document.getElementById('jadwal_konsultasi_id').value = konsultasiId;
    document.getElementById('jadwalModal').classList.remove('hidden');
}
function showSolusiModal(konsultasiId, solusi, tindakan, status) {
    document.getElementById('solusi_konsultasi_id').value = konsultasiId;
    document.getElementById('solusi_text').value = solusi;
    if (tindakan) document.getElementById('solusi_tindakan').value = tindakan;
    document.getElementById('solusiModal').classList.remove('hidden');
}
</script>
@endsection
