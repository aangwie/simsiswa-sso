@extends('layouts.app')

@section('title', 'Manajemen Siswa - SIMSiswa')
@section('header', 'Data Siswa')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <!-- Header/Toolbar -->
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Daftar Siswa</h2>
            <p class="text-sm text-slate-500">Kelola data seluruh siswa beserta status dan kelasnya.</p>
        </div>
        <div>
            <a href="{{ route('students.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 rounded-xl transition-all shadow-sm shadow-indigo-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Siswa
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 border-b border-green-100 flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Search Filter -->
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
        <form action="{{ route('students.index') }}" method="GET" class="relative" x-data>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama, NIS, atau NISN..."
                    class="w-full pl-11 pr-20 py-2.5 rounded-xl border border-slate-200 bg-white shadow-sm text-sm focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"
                    x-on:input.debounce.500ms="$el.form.submit()">
                @if(request('search'))
                <a href="{{ route('students.index') }}" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-red-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => ($sort == 'name' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                            Nama Siswa
                            @if($sort == 'name')
                                @if($direction == 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                @endif
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-4 font-medium">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'nisn', 'direction' => ($sort == 'nisn' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                            NISN
                            @if($sort == 'nisn')
                                @if($direction == 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                @endif
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-4 font-medium">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'class', 'direction' => ($sort == 'class' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-indigo-600 transition-colors">
                            Kelas
                            @if($sort == 'class')
                                @if($direction == 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                @endif
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-4 font-medium">L/P</th>
                    <th class="px-6 py-4 font-medium">Status / Lulus</th>
                    <th class="px-6 py-4 font-medium text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($students as $student)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-medium text-slate-800">{{ $student->name }}</p>
                        <p class="text-xs text-slate-500">{{ $student->alamat ?? 'Alamat belum diatur' }}</p>
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        {{ $student->nisn ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        {{ $student->schoolClass->name ?? 'Belum ada kelas' }}
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        {{ $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        @if($student->status_lulus)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ strtolower($student->status_lulus) == 'lulus' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }} ml-1">
                            {{ ucfirst($student->status_lulus) }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" 
                                onclick="showStudentDetail('{{ $student->id }}')"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
                                title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <a href="{{ route('students.edit', $student->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                        Belum ada data siswa.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($students->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $students->links() }}
    </div>
    @endif
</div>

<script>
function showStudentDetail(id) {
    Swal.fire({
        title: 'Memuat Data...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            fetch(`/students/${id}`)
                .then(response => response.text())
                .then(html => {
                    Swal.fire({
                        title: 'Detail Siswa',
                        html: html,
                        width: '800px',
                        showConfirmButton: false,
                        showCloseButton: true,
                        customClass: {
                            popup: 'rounded-2xl shadow-xl'
                        }
                    });
                })
                .catch(error => {
                    Swal.fire('Error', 'Gagal memuat data.', 'error');
                });
        }
    });
}
</script>
@endsection
