@extends('layouts.app')

@section('title', 'Jadwal Pengumuman - SIMSiswa')
@section('header', 'Pengaturan Jadwal Pengumuman')

@section('content')
    <div class="space-y-8 max-w-5xl mx-auto">
        <!-- Success Alert -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm animate-fade-in-down flex items-center gap-3">
                <svg class="h-5 w-5 text-green-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Form Card (Fully Horizontal Layout) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-800">Atur Waktu Pengumuman</h2>
            </div>

            <form action="{{ route('settings.jadwal-pengumuman.update') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Info Box -->
                <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 flex gap-3 text-indigo-800 text-xs leading-relaxed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <span class="font-bold block mb-0.5">Informasi Pengaturan Jadwal</span>
                        Kosongkan kedua pilihan input di bawah jika Anda ingin pengumuman kelulusan langsung dibuka untuk umum tanpa batasan waktu dan hitung mundur.
                    </div>
                </div>

                <!-- Horizontal Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    <!-- Tanggal Pengumuman -->
                    <div class="space-y-2">
                        <label for="jadwal_pengumuman_tanggal" class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">
                            Tanggal Pengumuman
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="date" id="jadwal_pengumuman_tanggal" name="jadwal_pengumuman_tanggal" 
                                value="{{ $settings['jadwal_pengumuman_tanggal'] ?? '' }}"
                                class="w-full pl-11 pr-4 py-2.5 rounded-xl border-slate-200 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all">
                        </div>
                        <p class="text-[10px] text-slate-400">Pilih tanggal akses kelulusan dibuka.</p>
                    </div>

                    <!-- Jam Pengumuman -->
                    <div class="space-y-2">
                        <label for="jadwal_pengumuman_jam" class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">
                            Jam Pengumuman
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input type="time" id="jadwal_pengumuman_jam" name="jadwal_pengumuman_jam" 
                                value="{{ $settings['jadwal_pengumuman_jam'] ?? '' }}"
                                class="w-full pl-11 pr-4 py-2.5 rounded-xl border-slate-200 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm transition-all">
                        </div>
                        <p class="text-[10px] text-slate-400">Pilih waktu/jam akses dibuka.</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col justify-end">
                        <label class="block text-xs font-semibold text-transparent select-none md:block hidden">Aksi</label>
                        <div class="flex gap-2">
                            <button type="button" id="btn-reset-jadwal"
                                class="flex-1 py-2.5 bg-white border border-red-200 text-red-600 font-bold rounded-xl hover:bg-red-50 active:bg-red-100 transition-all text-xs tracking-wider uppercase flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset
                            </button>
                            <button type="submit"
                                class="flex-1 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 active:bg-indigo-800 transition-all shadow-lg shadow-indigo-100 text-xs tracking-wider uppercase flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Simpan
                            </button>
                        </div>
                        <p class="text-[10px] text-transparent select-none md:block hidden">&nbsp;</p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview Card (Horizontal Split Layout) -->
        <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white rounded-2xl shadow-xl p-6 relative overflow-hidden border border-indigo-950">
            <!-- Background decorative elements -->
            <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-indigo-500/10 blur-xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-purple-500/10 blur-xl pointer-events-none"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10 items-center">
                <!-- Info Section (Left) -->
                <div class="space-y-4">
                    <span class="px-2.5 py-1 bg-white/10 rounded-full text-[10px] font-bold uppercase tracking-wider text-indigo-300">
                        Pratinjau Status
                    </span>
                    <h3 class="text-xl font-bold mt-2">Live Countdown</h3>
                    <p class="text-xs text-indigo-200/70">Status akses halaman kelulusan siswa berdasarkan waktu server saat ini.</p>
                    
                    @php
                        $tanggal = $settings['jadwal_pengumuman_tanggal'] ?? null;
                        $jam = $settings['jadwal_pengumuman_jam'] ?? null;
                        $hasJadwal = $tanggal && $jam;
                        $targetIso = $hasJadwal ? $tanggal . 'T' . $jam . ':00' : null;
                    @endphp

                    <div class="bg-white/5 rounded-xl p-4 border border-white/10 space-y-3">
                        <div class="flex justify-between items-center gap-4">
                            <span class="text-[10px] text-indigo-300/80 uppercase font-semibold">Jadwal Pengumuman</span>
                            <span class="text-xs font-bold text-white text-right">
                                @if($hasJadwal)
                                    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }} pukul {{ $jam }} WIB
                                @else
                                    <span class="text-green-400 font-medium">Bebas Akses (Selalu Terbuka)</span>
                                @endif
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-[10px] text-indigo-300/80 uppercase font-semibold">Status Saat Ini</span>
                            <span id="admin-status-badge" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-700 text-slate-300">
                                Mengkalkulasi...
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Countdown Section (Right) -->
                <div class="flex flex-col justify-center">
                    <div id="admin-countdown-wrapper" class="hidden">
                        <span class="block text-[10px] text-indigo-300/80 uppercase font-semibold mb-3 text-center md:text-left">Sisa Waktu Hitung Mundur</span>
                        <div class="grid grid-cols-4 gap-3 text-center">
                            <div class="bg-white/5 rounded-xl p-3 border border-white/10 shadow-lg">
                                <span id="admin-cd-days" class="block text-2xl md:text-3xl font-extrabold text-white leading-none">00</span>
                                <span class="text-[9px] text-indigo-300 uppercase font-bold tracking-wider mt-1.5 block">Hari</span>
                            </div>
                            <div class="bg-white/5 rounded-xl p-3 border border-white/10 shadow-lg">
                                <span id="admin-cd-hours" class="block text-2xl md:text-3xl font-extrabold text-white leading-none">00</span>
                                <span class="text-[9px] text-indigo-300 uppercase font-bold tracking-wider mt-1.5 block">Jam</span>
                            </div>
                            <div class="bg-white/5 rounded-xl p-3 border border-white/10 shadow-lg">
                                <span id="admin-cd-minutes" class="block text-2xl md:text-3xl font-extrabold text-white leading-none">00</span>
                                <span class="text-[9px] text-indigo-300 uppercase font-bold tracking-wider mt-1.5 block">Menit</span>
                            </div>
                            <div class="bg-white/5 rounded-xl p-3 border border-white/10 shadow-lg">
                                <span id="admin-cd-seconds" class="block text-2xl md:text-3xl font-extrabold text-white leading-none">00</span>
                                <span class="text-[9px] text-indigo-300 uppercase font-bold tracking-wider mt-1.5 block">Detik</span>
                            </div>
                        </div>
                    </div>

                    <div id="admin-accessible-wrapper" class="hidden bg-green-500/10 border border-green-500/20 rounded-2xl p-5 text-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-bold text-green-400">Akses Dibuka</p>
                        <p class="text-[11px] text-green-300/80 mt-1">Halaman Cek Kelulusan aktif & dapat diakses sepenuhnya oleh siswa.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($targetIso)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const targetDateStr = "{{ $targetIso }}";
            const targetDate = new Date(targetDateStr);
            
            const badge = document.getElementById('admin-status-badge');
            const countdownWrapper = document.getElementById('admin-countdown-wrapper');
            const accessibleWrapper = document.getElementById('admin-accessible-wrapper');
            
            const cdDays = document.getElementById('admin-cd-days');
            const cdHours = document.getElementById('admin-cd-hours');
            const cdMinutes = document.getElementById('admin-cd-minutes');
            const cdSeconds = document.getElementById('admin-cd-seconds');

            function updateTimer() {
                const now = new Date();
                const diff = targetDate - now;

                if (diff <= 0) {
                    badge.className = "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[10px] font-bold bg-green-500 text-white";
                    badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Terbuka';
                    countdownWrapper.classList.add('hidden');
                    accessibleWrapper.classList.remove('hidden');
                    return;
                }

                badge.className = "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-white";
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Tertutup (Menunggu Jadwal)';
                countdownWrapper.classList.remove('hidden');
                accessibleWrapper.classList.add('hidden');

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                cdDays.textContent = String(days).padStart(2, '0');
                cdHours.textContent = String(hours).padStart(2, '0');
                cdMinutes.textContent = String(minutes).padStart(2, '0');
                cdSeconds.textContent = String(seconds).padStart(2, '0');
            }

            updateTimer();
            setInterval(updateTimer, 1000);
        });
    </script>
    @else
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const badge = document.getElementById('admin-status-badge');
            const accessibleWrapper = document.getElementById('admin-accessible-wrapper');
            
            badge.className = "inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[10px] font-bold bg-green-500 text-white";
            badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Terbuka';
            accessibleWrapper.classList.remove('hidden');
        });
    </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resetBtn = document.getElementById('btn-reset-jadwal');
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Reset Jadwal Pengumuman?',
                        text: 'Jadwal akan dikosongkan dan akses kelulusan akan langsung terbuka untuk umum.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Reset Jadwal',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('jadwal_pengumuman_tanggal').value = '';
                            document.getElementById('jadwal_pengumuman_jam').value = '';
                            document.querySelector('form').submit();
                        }
                    });
                });
            }
        });
    </script>
@endsection
