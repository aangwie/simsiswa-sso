<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $websiteLogo = \App\Models\Setting::where('key', 'website_logo')->first()?->value;
        $websiteName = \App\Models\Setting::where('key', 'website_name')->first()?->value ?? 'SIMSiswa';
    @endphp
    <title>Hasil Pencarian NISN - {{ $websiteName }}</title>
    @if($websiteLogo)
        <link rel="icon" href="{{ $websiteLogo }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <meta name="description" content="Hasil Pencarian Kartu NISN">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body
    class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 flex flex-col relative overflow-x-hidden text-white">

    <!-- Top Navigation Bar -->
    <nav class="relative z-50 w-full bg-slate-900/50 border-b border-white/10 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
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
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col items-center px-4 sm:px-6 lg:px-8 relative z-10 py-10">

        <div class="w-full max-w-2xl">
            <div class="bg-white/[0.07] backdrop-blur-2xl rounded-3xl border border-white/10 shadow-2xl p-8">
                <h2 class="text-2xl font-bold mb-6 text-center text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Data Siswa Ditemukan</h2>

                <div class="space-y-4 bg-slate-900/50 p-6 rounded-2xl border border-white/5">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-indigo-200/70">Nama</div>
                        <div class="col-span-2 font-semibold">{{ $student->name }}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-indigo-200/70">NISN</div>
                        <div class="col-span-2 font-semibold">{{ $student->nisn }}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-indigo-200/70">Tempat Lahir</div>
                        <div class="col-span-2 font-semibold">{{ $student->tempat_lahir }}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-indigo-200/70">Tanggal Lahir</div>
                        <div class="col-span-2 font-semibold">{{ \Carbon\Carbon::parse($student->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-indigo-200/70">Jenis Kelamin</div>
                        <div class="col-span-2 font-semibold">{{ strtolower(substr($student->gender, 0, 1)) == 'p' ? 'Perempuan' : 'Laki-Laki' }}</div>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('cetak-nisn.index') }}" class="px-6 py-3 rounded-xl border border-white/20 text-white hover:bg-white/10 transition text-center font-medium">
                        Kembali
                    </a>
                    <a href="{{ route('cetak-nisn.pdf', $student->id) }}" target="_blank" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition text-center font-medium shadow-lg shadow-indigo-500/30 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                        </svg>
                        Cetak PDF
                    </a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
