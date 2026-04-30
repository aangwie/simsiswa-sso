@extends('layouts.app')

@section('title', 'SKL - SIMSiswa')
@section('header', 'Surat Keterangan Lulus (SKL)')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Title / Header -->
        <div class="text-center md:absolute md:left-1/2 md:-translate-x-1/2 w-full md:w-auto">
            <h2 class="text-lg font-bold text-slate-800">
                Data SKL Kumulatif Kelas {{ $class->name }}
            </h2>
            <p class="text-xs text-slate-500 mt-1">Rata-rata = Total Nilai Mapel / Jumlah Semester yang Diikuti</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center gap-2 w-full md:w-auto justify-end">
            <button type="button" onclick="showExportModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-indigo-600 rounded-xl transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="hidden md:inline">Ekspor SKL (Excel)</span>
            </button>
        </div>
    </div>

    <!-- Alert Error -->
    @if($errors->any())
    <div class="bg-red-50 text-red-700 p-4 border border-red-100 flex items-start gap-3 rounded-2xl shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <p class="text-sm font-medium">Terjadi kesalahan:</p>
            <ul class="text-sm list-disc list-inside mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border-b border-slate-200">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 text-sm border-y border-slate-200">
                        <th class="px-4 py-3 font-semibold border-r border-slate-200 w-64 min-w-[200px] whitespace-nowrap sticky left-0 bg-slate-50 z-10">Nama Siswa</th>
                        @foreach($subjects as $subject)
                            <th class="px-3 py-3 font-semibold border-r border-slate-200 text-center text-xs min-w-[80px]" title="{{ $subject->name }}">
                                {{ !empty($subject->code) ? $subject->code : \Str::limit($subject->name, 10, '') }}
                            </th>
                        @endforeach
                        <th class="px-4 py-3 font-semibold border-r border-slate-200 text-center whitespace-nowrap bg-indigo-50 text-indigo-700">Rata-Rata</th>
                        <th class="px-4 py-3 font-semibold border-r border-slate-200 text-center whitespace-nowrap bg-amber-50 text-amber-700">Nilai USP</th>
                        <th class="px-4 py-3 font-semibold border-r border-slate-200 text-center whitespace-nowrap bg-emerald-50 text-emerald-700">Rata-Rata Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($students as $student)
                        @php
                            $totalGrade = 0;
                            $countGrade = 0;
                            $totalUsp = 0;
                            $countUsp = 0;
                            $totalRataAkhir = 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-2 border-r border-slate-200 sticky left-0 bg-white font-medium text-slate-800">
                                {{ $student->name }}
                            </td>
                            @foreach($subjects as $subject)
                                @php
                                    $key = $student->id . '_' . $subject->id;
                                    $gradeValue = isset($existingGrades[$key]) ? round(floatval($existingGrades[$key]->grade)) : 0;
                                    $totalGrade += $gradeValue;
                                    $countGrade++;

                                    $uspValue = isset($uspGrades[$key]) ? floatval($uspGrades[$key]->grade) : 0;
                                    if ($uspValue > 0) {
                                        $totalUsp += $uspValue;
                                        $countUsp++;
                                    }

                                    $rataAkhir = ($gradeValue + $uspValue) / 2;
                                    $totalRataAkhir += $rataAkhir;
                                @endphp
                                <td class="px-1 py-1 border-r border-slate-200 text-center font-medium text-slate-600">
                                    {{ $gradeValue > 0 ? number_format($gradeValue, 0) : '-' }}
                                </td>
                            @endforeach
                            @php
                                $average = $countGrade > 0 ? round($totalGrade / $countGrade, 2) : 0;
                                $avgUsp = $countUsp > 0 ? round($totalUsp / $countUsp, 2) : 0;
                                $avgRataAkhir = $countGrade > 0 ? round($totalRataAkhir / $countGrade, 2) : 0;
                            @endphp
                            <td class="px-4 py-2 border-r border-slate-200 text-center font-bold text-indigo-600 bg-indigo-50/30">
                                {{ number_format($average, 2) }}
                            </td>
                            <td class="px-4 py-2 border-r border-slate-200 text-center font-bold text-amber-600 bg-amber-50/30">
                                {{ number_format($avgUsp, 2) }}
                            </td>
                            <td class="px-4 py-2 border-r border-slate-200 text-center font-bold text-emerald-600 bg-emerald-50/30">
                                {{ number_format($avgRataAkhir, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($subjects) + 4 }}" class="px-6 py-8 text-center text-slate-500 bg-white">
                                Belum ada siswa aktif di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeExportModal()"></div>
    
    <div class="relative bg-white rounded-2xl shadow-xl w-11/12 md:w-1/4 md:min-w-[400px] overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800" id="modal-title">Konfigurasi Ekspor SKL</h3>
            <button type="button" onclick="closeExportModal()" class="text-slate-400 hover:text-slate-500">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form action="{{ route('skl.export-excel', $class->id) }}" method="GET">
            
            <div class="p-6 space-y-4">
                <div class="bg-indigo-50 text-indigo-700 p-4 rounded-xl text-sm border border-indigo-100">
                    <p>Silakan tentukan minimum nilai kelulusan. Siswa dengan nilai rata-rata <strong>lebih atau sama dengan</strong> nilai ini akan dinyatakan LULUS (L).</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Minimum Nilai Kelulusan</label>
                    <input type="number" name="min_grade" value="65" min="0" max="100" step="0.01" required class="block w-full text-base px-4 py-2 border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeExportModal()" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Ekspor Excel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showExportModal() {
    document.getElementById('exportModal').classList.remove('hidden');
}
function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
}
</script>
@endsection
