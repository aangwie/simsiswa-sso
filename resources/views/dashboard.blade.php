@extends('layouts.app')

@section('title', 'Dashboard - SIMSiswa')
@section('header', 'Overview Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Welcome Section -->
    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-slate-100 relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Selamat datang, {{ auth()->user()->name ?? 'User' }}! 👋</h1>
                <p class="text-slate-500 mt-1 md:text-lg">Berikut adalah ringkasan data siswa pada sistem pendaftaran Anda.</p>
            </div>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 mr-2 animate-pulse"></span>
                    Sistem Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-4 gap-4 md:gap-6">
        
        <!-- Total Students -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group relative overflow-hidden">
            <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3 md:mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-slate-500 text-xs md:text-sm font-medium">Total Siswa</h3>
                <div class="mt-1 md:mt-2 flex items-baseline gap-2">
                    <span class="text-xl md:text-3xl font-bold text-slate-800">{{ number_format($totalStudents) }}</span>
                </div>
            </div>
        </div>

        <!-- Male Students -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group relative overflow-hidden">
            <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-50 rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-3 md:mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-slate-500 text-xs md:text-sm font-medium">Laki-Laki</h3>
                <div class="mt-1 md:mt-2 flex items-baseline gap-2">
                    <span class="text-xl md:text-3xl font-bold text-slate-800">{{ number_format($maleStudents) }}</span>
                </div>
            </div>
        </div>

        <!-- Female Students -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group relative overflow-hidden">
            <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-pink-100 to-pink-50 rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center mb-3 md:mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-slate-500 text-xs md:text-sm font-medium">Perempuan</h3>
                <div class="mt-1 md:mt-2 flex items-baseline gap-2">
                    <span class="text-xl md:text-3xl font-bold text-slate-800">{{ number_format($femaleStudents) }}</span>
                </div>
            </div>
        </div>

        <!-- Graduated & Mutated Students -->
        <div class="bg-white rounded-2xl p-4 md:p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow group relative overflow-hidden">
            <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-green-100 to-green-50 rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center mb-3 md:mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-slate-500 text-xs md:text-sm font-medium truncate">Lulus & Mutasi</h3>
                <div class="mt-1 md:mt-2 flex items-baseline gap-2">
                    <span class="text-xl md:text-3xl font-bold text-slate-800">{{ number_format($graduatedAndMutatedStudents) }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 md:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Grafik Siswa per Tahun Masuk</h2>
                <p class="text-sm text-slate-500">Pertumbuhan jumlah siswa setiap tahunnya.</p>
            </div>
            
            <div class="p-2 bg-slate-50 rounded-lg shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
        </div>
        
        <div class="relative h-80 w-full">
            <canvas id="enrollmentChart"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('enrollmentChart').getContext('2d');
        
        // Data from controller
        const labels = @json($chartLabels);
        const data = @json($chartData);
        
        // Gradient for chart area
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.5)'); // Indigo 600
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.05)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Siswa Masuk',
                    data: data,
                    borderColor: '#4f46e5', // Indigo 600
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Smooth curve
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 14, family: "'Inter', sans-serif" },
                        bodyFont: { size: 13, family: "'Inter', sans-serif" },
                        displayColors: false,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            font: { family: "'Inter', sans-serif", size: 12 },
                            color: '#64748b'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false,
                        },
                        ticks: {
                            font: { family: "'Inter', sans-serif", size: 12 },
                            color: '#64748b',
                            stepSize: 1
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    });
</script>
@endpush
