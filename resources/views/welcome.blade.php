<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $websiteLogo = \App\Models\Setting::where('key', 'website_logo')->first()?->value;
        $websiteName = \App\Models\Setting::where('key', 'website_name')->first()?->value ?? 'SIMSiswa';
    @endphp
    <title>Beranda - {{ $websiteName }}</title>
    @if($websiteLogo)
        <link rel="icon" href="{{ $websiteLogo }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <meta name="description" content="Portal Sistem Informasi Siswa">
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

        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float2 { animation: float2 5s ease-in-out infinite 1s; }
        .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-fadeInUp-delay { animation: fadeInUp 0.8s ease-out 0.2s forwards; opacity: 0; }
        .animate-fadeInUp-delay2 { animation: fadeInUp 0.8s ease-out 0.4s forwards; opacity: 0; }
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
                    <a href="{{ route('cetak-nisn.index') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition">Cetak NISN</a>
                    
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
                            <a href="{{ route('cetak-nisn.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-white/10">Cetak NISN</a>
                            <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-400 hover:text-indigo-300 hover:bg-white/10">Login Admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center animate-fadeInUp">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight mb-4">
                Selamat Datang di <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">{{ $websiteName }}</span>
            </h1>
            <p class="mt-4 text-base sm:text-lg md:text-xl text-indigo-200/70 max-w-2xl mx-auto mb-10">
                Portal layanan informasi siswa yang terintegrasi. Silakan pilih layanan yang Anda butuhkan melalui menu di atas.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fadeInUp-delay">
                <a href="{{ route('cek-kelulusan') }}" class="inline-flex justify-center items-center gap-2 py-3 px-6 rounded-2xl text-base font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-slate-900 transition-all duration-300 transform hover:-translate-y-1 shadow-xl shadow-indigo-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Cek SKL
                </a>
                
                <a href="{{ route('cetak-nisn.index') }}" class="inline-flex justify-center items-center gap-2 py-3 px-6 rounded-2xl text-base font-bold text-white bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/20 hover:border-white/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white/50 focus:ring-offset-slate-900 transition-all duration-300 transform hover:-translate-y-1 shadow-xl shadow-black/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    Cetak NISN
                </a>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="relative z-10 py-6 text-center border-t border-white/10 mt-auto">
        <p class="text-sm text-indigo-300/30">&copy; {{ date('Y') }} {{ $websiteName }}. All rights reserved.</p>
    </footer>

</body>
</html>
