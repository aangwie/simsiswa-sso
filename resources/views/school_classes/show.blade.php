@extends('layouts.app')

@section('title', 'Detail Kelas - SIMSiswa')
@section('header', 'Daftar Siswa Kelas: ' . $schoolClass->name)

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden" 
     x-data="{ 
        search: '', 
        sortCol: 'name', 
        sortAsc: true,
        perPage: 10,
        currentPage: 1,
        students: {{ $schoolClass->students->map(fn($s) => [
            'name' => $s->name,
            'nis' => $s->nis ?? '-',
            'gender' => $s->gender == 'male' ? 'Laki-laki' : 'Perempuan',
            'is_active' => $s->is_active,
            'status_lulus' => $s->status_lulus
        ])->toJson() }},
        get filteredStudents() {
            let filtered = this.students.filter(s => 
                s.name.toLowerCase().includes(this.search.toLowerCase()) || 
                s.nis.toLowerCase().includes(this.search.toLowerCase())
            );
            return filtered.sort((a, b) => {
                let valA = a[this.sortCol];
                let valB = b[this.sortCol];
                if (valA < valB) return this.sortAsc ? -1 : 1;
                if (valA > valB) return this.sortAsc ? 1 : -1;
                return 0;
            });
        },
        get pagedStudents() {
            let start = (this.currentPage - 1) * this.perPage;
            return this.filteredStudents.slice(start, start + parseInt(this.perPage));
        },
        get totalPages() {
            return Math.ceil(this.filteredStudents.length / this.perPage);
        },
        get startItem() {
            return this.filteredStudents.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
        },
        get endItem() {
            let end = this.currentPage * this.perPage;
            return end > this.filteredStudents.length ? this.filteredStudents.length : end;
        },
        toggleSort(col) {
            if (this.sortCol === col) this.sortAsc = !this.sortAsc;
            else { this.sortCol = col; this.sortAsc = true; }
            this.currentPage = 1;
        },
        updatePerPage() {
            this.currentPage = 1;
        }
     }">
    
    <!-- Top Bar (DataTables Style) -->
    <div class="p-6 border-b border-slate-100 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Siswa Kelas {{ $schoolClass->name }}</h2>
                <p class="text-sm text-slate-500">Tingkat: {{ $schoolClass->grade }} | TA: {{ $schoolClass->academic_year }}</p>
            </div>
            <a href="{{ route('classes.index') }}" class="text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors whitespace-nowrap">
                &larr; Kembali
            </a>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-4 border-t border-slate-50">
            <div class="flex items-center gap-2 text-sm text-slate-600">
                <span>Tampilkan</span>
                <select x-model="perPage" @change="updatePerPage" class="bg-slate-50 border-none rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 py-1 pl-3 pr-8">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entri</span>
            </div>
            
            <div class="relative">
                <input type="text" x-model="search" @input="currentPage = 1" placeholder="Cari siswa..." 
                       class="pl-10 pr-4 py-2 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 w-full md:w-64 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-widest font-bold">
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('name')">
                        <div class="flex items-center gap-1">
                            Nama Siswa
                            <svg x-show="sortCol === 'name'" :class="sortAsc ? '' : 'rotate-180'" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </th>
                    <th class="px-6 py-4 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('nis')">
                        <div class="flex items-center gap-1">
                            NIS
                            <svg x-show="sortCol === 'nis'" :class="sortAsc ? '' : 'rotate-180'" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </th>
                    <th class="px-6 py-4">Gender</th>
                    <th class="px-6 py-4">Status / Lulus</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                <template x-for="(student, index) in pagedStudents" :key="index">
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-slate-400 font-mono" x-text="(currentPage - 1) * perPage + index + 1"></td>
                        <td class="px-6 py-4 font-semibold text-slate-800" x-text="student.name"></td>
                        <td class="px-6 py-4 text-slate-600" x-text="student.nis"></td>
                        <td class="px-6 py-4 text-slate-600" x-text="student.gender"></td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                Aktif
                            </span>
                            <template x-if="student.status_lulus">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 ml-1" x-text="student.status_lulus">
                                </span>
                            </template>
                        </td>
                    </tr>
                </template>
                <tr x-show="pagedStudents.length === 0">
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <p class="text-sm">Tidak ada siswa yang sesuai pencarian.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Bottom Bar (DataTables Style) -->
    <div class="p-6 bg-slate-50 border-t border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="text-sm text-slate-500">
            Menampilkan <span class="font-bold text-slate-800" x-text="startItem"></span> 
            sampai <span class="font-bold text-slate-800" x-text="endItem"></span> 
            dari <span class="font-bold text-slate-800" x-text="filteredStudents.length"></span> entri
        </div>

        <div class="flex items-center gap-1" x-show="totalPages > 1">
            <button @click="currentPage = Math.max(1, currentPage - 1)" 
                    :disabled="currentPage === 1"
                    class="px-3 py-1 text-sm font-medium rounded-lg border border-slate-200 transition-all hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed">
                Sebelumnya
            </button>
            <div class="flex items-center gap-1">
                <template x-for="p in totalPages" :key="p">
                    <button @click="currentPage = p" 
                            class="w-8 h-8 flex items-center justify-center text-sm font-medium rounded-lg transition-all"
                            :class="currentPage === p ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'text-slate-600 hover:bg-white border border-transparent hover:border-slate-200'"
                            x-text="p">
                    </button>
                </template>
            </div>
            <button @click="currentPage = Math.min(totalPages, currentPage + 1)" 
                    :disabled="currentPage === totalPages"
                    class="px-3 py-1 text-sm font-medium rounded-lg border border-slate-200 transition-all hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed">
                Berikutnya
            </button>
        </div>
    </div>
</div>
@endsection
