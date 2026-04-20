<div class="text-left">
    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
        <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h3 class="text-xl font-bold text-slate-800">{{ $student->name }}</h3>
            <p class="text-indigo-600 font-medium">Siswa {{ $student->schoolClass->name ?? 'Tanpa Kelas' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">NISN</p>
            <p class="text-slate-700 font-medium">{{ $student->nisn ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">NIS</p>
            <p class="text-slate-700 font-medium">{{ $student->nis ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Jenis Kelamin</p>
            <p class="text-slate-700 font-medium">{{ $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Lahir</p>
            <p class="text-slate-700 font-medium">{{ $student->tanggal_lahir ? (\Carbon\Carbon::parse($student->tanggal_lahir)->translatedFormat('d F Y')) : '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Status Keaktifan</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Status Kelulusan</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ strtolower($student->status_lulus) == 'lulus' ? 'bg-blue-100 text-blue-800' : (strtolower($student->status_lulus) == 'mutasi' ? 'bg-orange-100 text-orange-800' : 'bg-slate-100 text-slate-800') }}">
                {{ $student->status_lulus ? ucfirst($student->status_lulus) : 'Dalam Proses' }}
            </span>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Tahun Masuk</p>
            <p class="text-slate-700 font-medium">{{ $student->enrollment_year }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Kelas</p>
            <p class="text-slate-700 font-medium">{{ $student->schoolClass->name ?? '-' }} ({{ $student->schoolClass->academic_year ?? '-' }})</p>
        </div>
    </div>

    <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nama Ayah</p>
            <p class="text-slate-700 font-medium">{{ $student->nama_ayah ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nama Ibu</p>
            <p class="text-slate-700 font-medium">{{ $student->nama_ibu ?? '-' }}</p>
        </div>
        <div class="col-span-full">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Alamat Lengkap</p>
            <p class="text-slate-700">{{ $student->alamat ?? '-' }}</p>
        </div>
    </div>

    <div class="mt-8 flex justify-center">
        <a href="{{ route('students.export-pdf', $student->id) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-bold rounded-xl hover:from-red-600 hover:to-red-700 transition-all shadow-md shadow-red-100 uppercase text-xs tracking-widest">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Cetak PDF
        </a>
    </div>
</div>
