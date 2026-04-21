@extends('layouts.app')

@section('title', 'Entry Rapor - SIMSiswa')
@section('header', 'Entry Rapor')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Semester Selector -->
        <div class="relative w-full md:w-auto">
            <form action="{{ route('reports.show', $class->id) }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 w-full" id="semesterForm">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 px-4 py-2 hover:border-indigo-300 transition-colors flex items-center justify-between w-full md:w-64">
                    <select name="semester_id" onchange="document.getElementById('semesterForm').submit()" class="w-full bg-transparent outline-none text-sm font-medium text-slate-700 cursor-pointer appearance-none">
                        <option value="" disabled>Pilih Semester</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $currentSemester->id == $sem->id ? 'selected' : '' }}>
                                {{ $sem->name }}
                            </option>
                        @endforeach
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </form>
        </div>

        <!-- Title / Header -->
        <div class="text-center md:absolute md:left-1/2 md:-translate-x-1/2 w-full md:w-auto">
            <h2 class="text-lg font-bold text-slate-800">
                Entry Rapor {{ $currentSemester->name }} Kelas {{ $class->name }}
            </h2>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center gap-2 w-full md:w-auto justify-end">
            <a href="{{ route('reports.export-excel', ['class' => $class->id, 'semester_id' => $currentSemester->id]) }}" class="inline-flex items-center justify-center gap-2 px-2 md:px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-indigo-600 rounded-xl transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="hidden md:inline">Ekspor Rapor</span>
            </a>
            <button type="button" onclick="showImportModal()" class="inline-flex items-center justify-center gap-2 px-2 md:px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-indigo-600 rounded-xl transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span class="hidden md:inline">Import Rapor</span>
            </button>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 border border-green-100 flex items-center gap-3 rounded-2xl shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

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
        <form action="{{ route('reports.store', $class->id) }}" method="POST">
            @csrf
            <input type="hidden" name="semester_id" value="{{ $currentSemester->id }}">

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse border-b border-slate-200">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 text-sm border-y border-slate-200">
                            <th class="px-4 py-3 font-semibold border-r border-slate-200 w-64 min-w-[200px] whitespace-nowrap sticky left-0 bg-slate-50 z-10">Nama Siswa</th>
                            @foreach($subjects as $subject)
                                <!-- using code if available or short name -->
                                <th class="px-3 py-3 font-semibold border-r border-slate-200 text-center text-xs min-w-[80px]" title="{{ $subject->name }}">
                                    {{ !empty($subject->code) ? $subject->code : \Str::limit($subject->name, 10, '') }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($students as $student)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-2 border-r border-slate-200 sticky left-0 bg-white font-medium text-slate-800">
                                    {{ $student->name }}
                                </td>
                                @foreach($subjects as $subject)
                                    @php
                                        // Retrieve existing grade if any
                                        $key = $student->id . '_' . $subject->id;
                                        $gradeValue = isset($existingGrades[$key]) ? $existingGrades[$key]->grade : '';
                                    @endphp
                                    <td class="px-1 py-1 border-r border-slate-200 text-center">
                                        <input type="number" 
                                               name="grades[{{ $student->id }}][{{ $subject->id }}]" 
                                               value="{{ $gradeValue }}" 
                                               min="0" max="100"
                                               class="w-full md:w-16 mx-auto px-2 py-1.5 text-center text-sm rounded-lg border border-transparent hover:border-slate-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:bg-white bg-slate-50/50 transition-all outline-none"
                                               placeholder="-">
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($subjects) + 1 }}" class="px-6 py-8 text-center text-slate-500 bg-white">
                                    Belum ada siswa aktif di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->isNotEmpty() && $subjects->isNotEmpty())
            <div class="p-6 bg-slate-50/80 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="inline-flex justify-center items-center px-8 py-2.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 border border-yellow-300 shadow-sm text-sm font-bold rounded-xl transition-all w-full sm:w-auto">
                    Simpan
                </button>
            </div>
            @endif
        </form>
    </div>

</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 z-50 flex items-center justify-center hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeImportModal()"></div>
    
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800" id="modal-title">Import Data Rapor</h3>
            <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-500">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form action="{{ route('reports.import-excel', $class->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="semester_id" value="{{ $currentSemester->id }}">
            
            <div class="p-6 space-y-4">
                <div class="bg-indigo-50 text-indigo-700 p-4 rounded-xl text-sm border border-indigo-100">
                    <p class="font-semibold mb-2">Panduan Import:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Unduh template rapor untuk kelas <strong>{{ $class->name }}</strong>.</li>
                        <li>Isi nilai rapor pada kolom setiap pelajaran. Kosongkan jika belum ada nilai.</li>
                        <li>Format file harus berakhiran <strong>.xlsx</strong>.</li>
                        <li>Jangan mengubah susunan kolom ID Siswa & Nama.</li>
                    </ul>
                    <div class="mt-3">
                        <a href="{{ route('reports.import-template', $class->id) }}" class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 font-medium bg-white px-3 py-1.5 rounded-lg border border-indigo-200 shadow-sm transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Unduh Template Kelas {{ $class->name }}
                        </a>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pilih File Excel yang sudah diisi</label>
                    <input type="file" name="file" accept=".xlsx, .xls" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">Import Nilai</button>
            </div>
        </form>
    </div>
</div>

<script>
function showImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}
function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}
</script>
@endsection
