<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $websiteLogo = \App\Models\Setting::where('key', 'website_logo')->first()?->value;
        $websiteName = \App\Models\Setting::where('key', 'website_name')->first()?->value ?? 'SIMSiswa';
    @endphp
    <title>Hasil Kelulusan - {{ $websiteName }}</title>
    @if($websiteLogo)
        <link rel="icon" href="{{ $websiteLogo }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <meta name="description" content="Hasil pengecekan kelulusan siswa {{ $student->name }}.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(2deg);
            }
        }

        @keyframes float2 {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-15px) rotate(-2deg);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-float2 {
            animation: float2 5s ease-in-out infinite 1s;
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-fadeInUp-delay {
            animation: fadeInUp 0.8s ease-out 0.3s forwards;
            opacity: 0;
        }

        .animate-fadeInUp-delay2 {
            animation: fadeInUp 0.8s ease-out 0.5s forwards;
            opacity: 0;
        }

        .animate-fadeInScale {
            animation: fadeInScale 0.6s ease-out 0.2s forwards;
            opacity: 0;
        }

        .shimmer {
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.1) 50%, transparent 100%);
            background-size: 200% 100%;
            animation: shimmer 3s infinite;
        }

        .confetti-piece {
            position: fixed;
            width: 10px;
            height: 10px;
            top: -20px;
            z-index: 100;
            animation: confetti-fall linear forwards;
            pointer-events: none;
        }
    </style>
</head>

<body
    class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 flex flex-col relative overflow-x-hidden text-white"
    x-data="{ showSklModal: false }">

    <!-- Animated background elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div
            class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full bg-indigo-500/10 blur-[120px] animate-float">
        </div>
        <div
            class="absolute top-1/3 -right-20 w-[400px] h-[400px] rounded-full bg-purple-500/10 blur-[100px] animate-float2">
        </div>
        <div
            class="absolute -bottom-32 left-1/3 w-[450px] h-[450px] rounded-full bg-blue-500/10 blur-[120px] animate-float">
        </div>
        @if($isLulus)
            <div
                class="absolute top-1/4 left-1/4 w-[300px] h-[300px] rounded-full bg-emerald-500/10 blur-[100px] animate-float2">
            </div>
        @endif
    </div>

    <!-- Grid pattern overlay -->
    <div class="fixed inset-0 pointer-events-none opacity-[0.03]"
        style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
    </div>

    <!-- Top Navigation Bar -->
    <nav class="relative z-50 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo / Brand -->
                <div class="flex items-center gap-3">
                    @if($websiteLogo)
                        <div
                            class="w-10 h-10 flex-shrink-0 rounded-xl bg-white/10 backdrop-blur-sm p-1.5 flex items-center justify-center border border-white/20">
                            <img src="{{ $websiteLogo }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                    @else
                        <div
                            class="w-10 h-10 flex-shrink-0 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    @endif
                    <span
                        class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-white to-indigo-200">{{ $websiteName }}</span>
                </div>

                <!-- Login Button -->
                <a href="{{ route('login') }}" id="btn-login"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl hover:bg-white/20 hover:border-white/30 transition-all duration-300 shadow-lg shadow-black/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Login Admin
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 relative z-10 py-8 -mt-10">

        <!-- Status Badge -->
        <div class="animate-fadeInScale mb-6">
            @if($isLulus)
                <div
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 font-bold text-lg shadow-lg shadow-emerald-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    LULUS
                </div>
            @else
                <div
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-red-500/20 border border-red-500/30 text-red-300 font-bold text-lg shadow-lg shadow-red-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    TIDAK LULUS
                </div>
            @endif
        </div>

        <!-- Result Card -->
        <div class="w-full max-w-lg animate-fadeInUp">
            <div
                class="bg-white/[0.07] backdrop-blur-2xl rounded-3xl border border-white/10 shadow-2xl shadow-black/20 overflow-hidden">

                <!-- Card Header -->
                <div class="px-8 pt-8 pb-4 text-center">
                    <h1
                        class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-white to-indigo-200">
                        Hasil Pengecekan Kelulusan
                    </h1>
                </div>

                <!-- Student Info -->
                <div class="px-8 pb-6">
                    <div class="bg-white/5 rounded-2xl border border-white/10 p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-white/5 pb-3">
                            <span class="text-sm text-indigo-300/60 font-medium">Nama</span>
                            <span class="text-sm font-bold text-white">{{ $student->name }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-white/5 pb-3">
                            <span class="text-sm text-indigo-300/60 font-medium">NIS</span>
                            <span class="text-sm font-semibold text-indigo-200">{{ $student->nis ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-white/5 pb-3">
                            <span class="text-sm text-indigo-300/60 font-medium">NISN</span>
                            <span class="text-sm font-semibold text-indigo-200">{{ $student->nisn ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-white/5 pb-3">
                            <span class="text-sm text-indigo-300/60 font-medium">Kelas</span>
                            <span
                                class="text-sm font-semibold text-indigo-200">{{ $student->schoolClass->name ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-white/5 pb-3">
                            <span class="text-sm text-indigo-300/60 font-medium">Rata-Rata Akhir</span>
                            <span
                                class="text-sm font-bold {{ $isLulus ? 'text-emerald-400' : 'text-red-400' }}">{{ number_format($average, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-indigo-300/60 font-medium">Status</span>
                            @if($isLulus)
                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    LULUS
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-500/20 text-red-300 text-xs font-bold border border-red-500/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    TIDAK LULUS
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-8 pb-8 space-y-3">
                    @if($isLulus)
                        <button type="button" @click="showSklModal = true"
                            class="w-full flex justify-center items-center gap-2.5 py-3.5 px-4 rounded-2xl text-sm font-bold text-white bg-white/10 border border-white/20 hover:bg-white/20 hover:border-white/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white/50 focus:ring-offset-slate-900 transition-all duration-300 transform hover:-translate-y-0.5 shadow-xl shadow-black/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat SKL
                        </button>

                        <a href="{{ route('skl.cetak.pdf', $student->id) }}" target="_blank" id="btn-download-skl"
                            class="w-full flex justify-center items-center gap-2.5 py-3.5 px-4 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 focus:ring-offset-slate-900 transition-all duration-300 transform hover:-translate-y-0.5 shadow-xl shadow-emerald-500/30 hover:shadow-emerald-500/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download SKL (PDF)
                        </a>
                    @endif

                    <a href="{{ route('cek-kelulusan') }}" id="btn-cek-ulang"
                        class="w-full flex justify-center items-center gap-2.5 py-3.5 px-4 rounded-2xl text-sm font-semibold text-indigo-200 bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Cek Siswa Lain
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="relative z-10 py-6 text-center">
        <p class="text-sm text-indigo-300/30">&copy; {{ date('Y') }} {{ $websiteName }}. All rights reserved.</p>
    </footer>

    @if($isLulus)
        <!-- Modal SKL -->
        <div x-show="showSklModal" style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-transition.opacity>
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" @click="showSklModal = false"></div>

            <!-- Modal Panel -->
            <div class="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col h-[85vh]"
                x-transition.scale.origin.bottom>
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800">Pratinjau Surat Keterangan Lulus</h3>
                    <button type="button" @click="showSklModal = false"
                        class="text-slate-400 hover:text-red-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body / Iframe -->
                <div class="flex-1 bg-slate-100 p-2">
                    <iframe src="{{ route('skl.cetak.pdf', $student->id) }}"
                        class="w-full h-full rounded-xl border-0 shadow-inner"></iframe>
                </div>
            </div>
        </div>

        <!-- Confetti animation for graduated students -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const colors = ['#10b981', '#6366f1', '#8b5cf6', '#f59e0b', '#3b82f6', '#ec4899'];
                const shapes = ['circle', 'square'];

                for (let i = 0; i < 50; i++) {
                    setTimeout(() => {
                        const confetti = document.createElement('div');
                        confetti.className = 'confetti-piece';
                        confetti.style.left = Math.random() * 100 + 'vw';
                        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                        confetti.style.width = (Math.random() * 8 + 5) + 'px';
                        confetti.style.height = (Math.random() * 8 + 5) + 'px';
                        confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
                        confetti.style.animationDelay = (Math.random() * 0.5) + 's';
                        if (shapes[Math.floor(Math.random() * shapes.length)] === 'circle') {
                            confetti.style.borderRadius = '50%';
                        } else {
                            confetti.style.borderRadius = '2px';
                        }
                        document.body.appendChild(confetti);

                        setTimeout(() => confetti.remove(), 5500);
                    }, i * 60);
                }
            });
        </script>
    @endif

</body>

</html>