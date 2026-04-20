<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIM Siswa - Admin Dashboard')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full antialiased font-sans flex flex-col md:flex-row overflow-hidden bg-slate-50" 
      x-data="{ 
        sidebarOpen: false, 
        sidebarMinimized: false,
        manajemenOpen: {{ request()->routeIs('students.*', 'classes.*', 'subjects.*') ? 'true' : 'false' }}
      }">

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm transition-opacity md:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 bg-indigo-900 text-white transform transition-all duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col shadow-2xl"
           :class="{
               'translate-x-0': sidebarOpen, 
               '-translate-x-full': !sidebarOpen,
               'w-72': !sidebarMinimized,
               'w-20': sidebarMinimized
           }">
        
        <div class="flex items-center justify-between h-20 px-6 bg-indigo-950 border-b border-indigo-800/50 overflow-hidden">
            <div class="flex items-center gap-3 min-w-max">
                <div class="w-10 h-10 flex-shrink-0 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <h1 class="text-xl font-bold tracking-wider relative top-px text-transparent bg-clip-text bg-gradient-to-r from-white to-indigo-200">SIMSiswa</h1>
                </div>
            </div>
            
            <div class="flex gap-2">
                <!-- Minimize Button -->
                <button @click="sidebarMinimized = !sidebarMinimized" class="hidden md:flex p-1.5 rounded-lg bg-indigo-800/50 text-indigo-300 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform" :class="{'rotate-180': sidebarMinimized}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="sidebarOpen = false" class="md:hidden text-indigo-200 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1 custom-scrollbar overflow-x-hidden">
            <p x-show="!sidebarMinimized" class="px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">Main Menu</p>
            
            <a href="{{ route('dashboard') }}" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl border border-transparent 
                {{ request()->routeIs('dashboard') ? 'bg-indigo-800/60 text-white border-indigo-700/50 shadow-inner' : 'text-indigo-200 hover:bg-indigo-800/40 hover:text-white hover:border-indigo-700/30 transition-all' }}"
                :title="sidebarMinimized ? 'Dashboard' : ''">
                <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-indigo-300' : 'text-indigo-400 group-hover:text-indigo-300 transition-colors' }}" 
                    :class="{'mr-3': !sidebarMinimized}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">Dashboard</span>
            </a>
            
            <!-- Manajemen Submenu -->
            <div class="space-y-1">
                <button @click="manajemenOpen = !manajemenOpen" 
                    class="w-full group flex items-center justify-between px-3 py-3 text-sm font-medium rounded-xl border border-transparent text-indigo-200 hover:bg-indigo-800/40 hover:text-white hover:border-indigo-700/30 transition-all"
                    :class="{'bg-indigo-800/30': manajemenOpen}"
                    :title="sidebarMinimized ? 'Manajemen' : ''">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 flex-shrink-0 text-indigo-400 group-hover:text-indigo-300 transition-colors" 
                            :class="{'mr-3': !sidebarMinimized}"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">Manajemen</span>
                    </div>
                    <svg x-show="!sidebarMinimized" class="h-4 w-4 transform transition-transform" :class="{'rotate-180': manajemenOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div x-show="manajemenOpen && !sidebarMinimized" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="pl-11 pr-3 space-y-1">
                    <a href="{{ route('students.index') }}" class="block py-2 text-sm {{ request()->routeIs('students.*') ? 'text-white font-bold' : 'text-indigo-300 hover:text-white' }} transition-colors">Data Siswa</a>
                    <a href="{{ route('classes.index') }}" class="block py-2 text-sm {{ request()->routeIs('classes.*') ? 'text-white font-bold' : 'text-indigo-300 hover:text-white' }} transition-colors">Data Kelas</a>
                    <a href="{{ route('subjects.index') }}" class="block py-2 text-sm {{ request()->routeIs('subjects.*') ? 'text-white font-bold' : 'text-indigo-300 hover:text-white' }} transition-colors">Mata Pelajaran</a>
                </div>

                <!-- Floating Tooltip for Minimized Sidebar -->
                <div x-show="manajemenOpen && sidebarMinimized" class="absolute left-20 py-2 w-48 bg-indigo-900 rounded-xl shadow-xl border border-indigo-700 z-50">
                    <a href="{{ route('students.index') }}" class="block px-4 py-2 text-sm text-indigo-200 hover:bg-indigo-800 hover:text-white">Data Siswa</a>
                    <a href="{{ route('classes.index') }}" class="block px-4 py-2 text-sm text-indigo-200 hover:bg-indigo-800 hover:text-white">Data Kelas</a>
                    <a href="{{ route('subjects.index') }}" class="block px-4 py-2 text-sm text-indigo-200 hover:bg-indigo-800 hover:text-white">Mata Pelajaran</a>
                </div>
            </div>

            <a href="{{ route('settings.index') }}" class="group flex items-center px-3 py-3 text-sm font-medium rounded-xl {{ request()->routeIs('settings.*') ? 'bg-indigo-800/60 text-white border-indigo-700/50 shadow-inner' : 'text-indigo-200 border border-transparent hover:bg-indigo-800/40 hover:text-white hover:border-indigo-700/30 transition-all' }}"
                :title="sidebarMinimized ? 'Pengaturan' : ''">
                <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('settings.*') ? 'text-indigo-300' : 'text-indigo-400 group-hover:text-indigo-300 transition-colors' }}" 
                    :class="{'mr-3': !sidebarMinimized}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span x-show="!sidebarMinimized" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">Pengaturan</span>
            </a>
        </div>
        
        <!-- User Section Footer Sidebar -->
        <div class="px-4 py-5 bg-indigo-950/50 border-t border-indigo-800/50" :class="{'items-center': sidebarMinimized}">
            <div class="flex items-center gap-3 mb-4" :class="{'justify-center': sidebarMinimized}">
                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 p-0.5">
                    <div class="w-full h-full rounded-full bg-indigo-900 flex items-center justify-center border-2 border-indigo-950 text-white font-bold text-sm">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                </div>
                <div x-show="!sidebarMinimized" class="flex-1 min-w-0" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'User Name' }}</p>
                    <p class="text-xs text-indigo-300 truncate">{{ auth()->user()->role ?? 'Admin' }}</p>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="button" onclick="confirmLogout()" class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 hover:border-red-500/40 rounded-lg transition-colors"
                    :title="sidebarMinimized ? 'Logout' : ''">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="!sidebarMinimized">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50/50">
        
        <!-- Navbar -->
        <header class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-30 border-b border-slate-200">
            <div class="flex items-center justify-between h-20 px-6">
                
                <!-- Navbar Left -->
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </button>

                    <h2 class="text-xl font-bold text-slate-800 hidden sm:block">
                        @yield('header', 'Dashboard')
                    </h2>
                </div>

                <!-- Navbar Right -->
                <div class="flex items-center gap-4">
                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false" class="flex items-center gap-3 p-1 rounded-full hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <span class="text-sm font-medium text-slate-700 hidden sm:block pl-2">{{ auth()->user()->name ?? 'User Name' }}</span>
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center shadow-sm">
                                <span class="text-indigo-700 font-bold text-sm">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                        </button>
                        
                        <!-- Dropdown -->
                        <div x-show="userMenuOpen" 
                             x-transition:enter="transition ease-out duration-100" 
                             x-transition:enter-start="transform opacity-0 scale-95" 
                             x-transition:enter-end="transform opacity-100 scale-100" 
                             x-transition:leave="transition ease-in duration-75" 
                             x-transition:leave-start="transform opacity-100 scale-100" 
                             x-transition:leave-end="transform opacity-0 scale-95" 
                             class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 z-50 divide-y divide-slate-100"
                             style="display: none;">
                             
                            <div class="px-4 py-3">
                                <p class="text-sm text-slate-500">Signed in as</p>
                                <p class="text-sm font-medium text-slate-900 truncate">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                            </div>
                            
                            <div class="py-1">
                                <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">Profile Settings</a>
                            </div>
                            
                            <div class="py-1">
                                <button type="button" @click="confirmLogout()" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                                    Sign out
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <!-- Main Workspace -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto relative">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03] z-0 pointer-events-none"></div>
            
            <div class="relative z-10 p-6 md:p-8 max-w-7xl mx-auto min-h-[calc(100vh-140px)]">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="relative z-10 border-t border-slate-200 bg-white shadow-sm mt-auto">
                <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-500 font-medium">
                        &copy; {{ date('Y') }} SIMSiswa. All rights reserved.
                    </p>
                    <div class="flex items-center gap-4 text-xs font-medium text-slate-400 bg-slate-50 px-4 py-2 rounded-lg border border-slate-100">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            PHP v{{ $phpVersion ?? phpversion() }}
                        </div>
                        <div class="w-px h-4 bg-slate-300"></div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            MySQL {{ $mysqlVersion ?? 'v8+' }}
                        </div>
                    </div>
                </div>
            </footer>
        </main>
        
    </div>

    <!-- SweetAlert2 Logout Confirmation Script -->
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Sesi Anda akan diakhiri dan harus login kembali.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                customClass: {
                    title: 'text-slate-800 font-bold',
                    htmlContainer: 'text-slate-600',
                    confirmButton: 'shadow-lg shadow-indigo-500/30 rounded-xl px-6 py-2.5',
                    cancelButton: 'shadow-lg shadow-red-500/30 rounded-xl px-6 py-2.5',
                    popup: 'rounded-2xl border border-slate-100 shadow-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            })
        }
    </script>
    
    @stack('scripts')
</body>
</html>
