<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $websiteLogo = \App\Models\Setting::where('key', 'website_logo')->first()?->value;
        $websiteName = \App\Models\Setting::where('key', 'website_name')->first()?->value ?? 'SIMSiswa';
    @endphp
    <title>Cetak NISN - {{ $websiteName }}</title>
    @if($websiteLogo)
        <link rel="icon" href="{{ $websiteLogo }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <meta name="description" content="Cetak Kartu NISN dengan memasukkan NISN dan tanggal lahir.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        @keyframes float2 {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(-2deg); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.3); }
            50% { box-shadow: 0 0 40px rgba(99, 102, 241, 0.5); }
        }

        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float2 { animation: float2 5s ease-in-out infinite 1s; }
        .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-fadeInUp-delay { animation: fadeInUp 0.8s ease-out 0.2s forwards; opacity: 0; }
        .pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }
    </style>
</head>

<body
    class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 flex flex-col relative overflow-x-hidden text-white">

    <!-- Animated background elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full bg-indigo-500/10 blur-[120px] animate-float"></div>
        <div class="absolute top-1/3 -right-20 w-[400px] h-[400px] rounded-full bg-purple-500/10 blur-[100px] animate-float2"></div>
        <div class="absolute -bottom-32 left-1/3 w-[450px] h-[450px] rounded-full bg-blue-500/10 blur-[120px] animate-float"></div>
    </div>

    <!-- Grid pattern overlay -->
    <div class="fixed inset-0 pointer-events-none opacity-[0.03]"
        style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
    </div>

    <!-- Top Navigation Bar -->
    <nav class="relative z-50 w-full bg-slate-900/50 border-b border-white/10 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo / Brand -->
                <div class="flex items-center gap-3">
                    @if($websiteLogo)
                        <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-white/10 p-1.5 flex items-center justify-center border border-white/20">
                            <img src="{{ $websiteLogo }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                    @else
                        <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    @endif
                    <a href="{{ url('/') }}">
                        <span class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-white to-indigo-200">{{ $websiteName }}</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-4 items-center">
                    <a href="{{ route('cek-kelulusan') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition">Cek SKL</a>
                    <a href="{{ route('cetak-nisn.index') }}" class="px-3 py-2 rounded-md text-sm font-medium text-white bg-white/10 transition">Cetak NISN</a>
                    
                    <a href="{{ route('login') }}" class="ml-4 inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-500 transition-all duration-300 shadow-lg shadow-indigo-500/30">
                        Login Admin
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Open main menu</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!open">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="open" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    
                    <!-- Mobile Menu Panel -->
                    <div x-show="open" @click.away="open = false" class="absolute top-20 left-0 w-full bg-slate-900 border-b border-white/10 shadow-xl" style="display: none;">
                        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                            <a href="{{ route('cek-kelulusan') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-white/10">Cek SKL</a>
                            <a href="{{ route('cetak-nisn.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-white/10">Cetak NISN</a>
                            <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-400 hover:text-indigo-300 hover:bg-white/10">Login Admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 relative z-10 py-10">

        <!-- Header -->
        <div class="text-center mb-8 animate-fadeInUp">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-2xl shadow-indigo-500/40 mb-6 pulse-glow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-indigo-200 to-purple-200">Cetak Kartu NISN</span>
            </h1>
            <p class="mt-3 text-base sm:text-lg text-indigo-200/70 max-w-md mx-auto">
                Masukkan NISN dan Tanggal Lahir Anda untuk melihat dan mencetak Kartu NISN.
            </p>
        </div>

        <!-- Form Card -->
        <div class="w-full max-w-md animate-fadeInUp-delay">
            <div class="bg-white/[0.07] backdrop-blur-2xl rounded-3xl border border-white/10 shadow-2xl shadow-black/20 p-8 sm:p-10">

                @if ($errors->any())
                    <div class="mb-6 bg-red-500/10 backdrop-blur-sm border border-red-500/20 p-4 rounded-2xl">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-red-300">Pencarian Gagal</h3>
                                <div class="mt-1 text-sm text-red-300/80">
                                    @foreach ($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('cetak-nisn.check') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- NISN -->
                    <div>
                        <label for="nisn" class="block text-sm font-semibold text-indigo-100 mb-2">NISN</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-indigo-300/50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <input id="nisn" name="nisn" type="text" required value="{{ old('nisn') }}"
                                class="block w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-indigo-300/30 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 text-sm transition-all backdrop-blur-sm"
                                placeholder="Masukkan NISN Anda">
                        </div>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-semibold text-indigo-100 mb-2">Tanggal Lahir</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-indigo-300/50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input id="tanggal_lahir" name="tanggal_lahir" type="text" required value="{{ old('tanggal_lahir') }}"
                                class="block w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-indigo-300/30 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 text-sm transition-all backdrop-blur-sm [color-scheme:dark]"
                                placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="w-full flex justify-center items-center gap-2.5 py-3.5 px-4 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-slate-900 transition-all duration-300 transform hover:-translate-y-0.5 shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/50">
                            Cari Data Siswa
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="relative z-10 py-6 text-center border-t border-white/10 mt-auto">
        <p class="text-sm text-indigo-300/30">&copy; {{ date('Y') }} {{ $websiteName }}. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById('tanggal_lahir').addEventListener('input', function (e) {
            if (e.inputType === 'deleteContentBackward') return;

            let input = e.target.value.replace(/\D/g, '').substring(0, 8);
            let formatted = '';

            if (input.length > 4) {
                formatted = input.substring(0, 2) + '/' + input.substring(2, 4) + '/' + input.substring(4, 8);
            } else if (input.length > 2) {
                formatted = input.substring(0, 2) + '/' + input.substring(2, 4);
            } else {
                formatted = input;
            }

            e.target.value = formatted;
        });
    </script>
</body>
</html>
