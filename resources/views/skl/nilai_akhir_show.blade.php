@extends('layouts.app')

@section('title', 'Nilai Akhir - SIMSiswa')
@section('header', 'Nilai Akhir')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="text-center md:absolute md:left-1/2 md:-translate-x-1/2 w-full md:w-auto">
            <h2 class="text-lg font-bold text-slate-800">
                Nilai Akhir Kelas {{ $class->name }}
            </h2>
            <p class="text-xs text-slate-500 mt-1">NA = (Rata-rata Rapor × 60%) + (Nilai USP × 40%)</p>
        </div>
    </div>

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
                        <th class="px-4 py-3 font-semibold border-r border-slate-200 text-center whitespace-nowrap bg-emerald-50 text-emerald-700">Rata-Rata NA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($students as $student)
                        @php
                            $totalNA = 0;
                            $countNA = 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-2 border-r border-slate-200 sticky left-0 bg-white font-medium text-slate-800">
                                {{ $student->name }}
                            </td>
                            @foreach($subjects as $subject)
                                @php
                                    $key = $student->id . '_' . $subject->id;
                                    $raporAvg = isset($existingGrades[$key]) ? floatval($existingGrades[$key]->grade) : 0;
                                    $uspValue = isset($uspGrades[$key]) ? floatval($uspGrades[$key]->grade) : 0;
                                    
                                    // NA = (Rata-rata Rapor × 60%) + (Nilai USP × 40%)
                                    $nilaiAkhir = ($raporAvg * 0.6) + ($uspValue * 0.4);
                                    $nilaiAkhir = round($nilaiAkhir, 2);
                                    
                                    $totalNA += $nilaiAkhir;
                                    $countNA++;
                                @endphp
                                <td class="px-1 py-1 border-r border-slate-200 text-center font-medium text-slate-600">
                                    {{ $nilaiAkhir > 0 ? number_format($nilaiAkhir, 2) : '-' }}
                                </td>
                            @endforeach
                            @php
                                $avgNA = $countNA > 0 ? round($totalNA / $countNA, 2) : 0;
                            @endphp
                            <td class="px-4 py-2 border-r border-slate-200 text-center font-bold text-emerald-600 bg-emerald-50/30">
                                {{ number_format($avgNA, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($subjects) + 2 }}" class="px-6 py-8 text-center text-slate-500 bg-white">
                                Belum ada siswa aktif di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
