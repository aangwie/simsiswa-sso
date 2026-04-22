@extends('layouts.app')

@section('title', 'Pengaturan - SIMSiswa')
@section('header', 'Pengaturan Sistem')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">
    <!-- Success Alert -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm animate-fade-in-down">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Settings Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center gap-3">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-slate-800">Konfigurasi Sistem</h2>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" class="p-6 space-y-8">
            @csrf
            
            <!-- Git Configuration Section -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest border-b border-slate-50 pb-2">Konfigurasi GitHub</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">GitHub Access Token</label>
                        <input type="password" name="github_token" value="{{ $settings->where('key', 'github_token')->first()?->value }}" placeholder="ghp_xxxxxxxxxxxx" class="w-full rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2 text-sm font-mono">
                        <p class="text-[10px] text-slate-400">Personal Access Token (fine-grained) dengan izin read-only.</p>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">Repository URL</label>
                        <input type="text" name="github_repo_url" value="{{ $settings->where('key', 'github_repo_url')->first()?->value }}" placeholder="https://github.com/username/repo.git" class="w-full rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2 text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">Git Branch</label>
                        <input type="text" name="github_branch" value="{{ $settings->where('key', 'github_branch')->first()?->value ?? 'main' }}" placeholder="main" class="w-full rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2 text-sm">
                    </div>
                </div>
            </div>

            <!-- Other Settings Section -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest border-b border-slate-50 pb-2">Pengaturan Umum</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($settings->whereIn('key', ['tempat_cetak', 'tanggal_cetak', 'nomor_skl']) as $setting)
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700 tracking-wide uppercase">
                            {{ str_replace('_', ' ', $setting->key) }}
                        </label>
                        <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-2 text-sm">
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-8 py-2.5 bg-gradient-to-r from-purple-500 to-blue-500 text-white font-bold rounded-xl hover:from-purple-600 hover:to-blue-600 transition-all shadow-lg shadow-indigo-100 uppercase text-xs tracking-widest">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- GitHub Update Section -->
    <div class="bg-slate-900 rounded-2xl shadow-xl border border-slate-800 overflow-hidden" x-data="{ updating: false, output: '' }">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-slate-800 text-white rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Update Aplikasi</h2>
                    <p class="text-xs text-slate-400">Tarik pembaruan terbaru dari GitHub.</p>
                </div>
            </div>
            <button @click="updateApp()" :disabled="updating" class="inline-flex items-center gap-2 px-6 py-2 bg-white text-slate-900 font-bold rounded-xl hover:bg-slate-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed uppercase text-[10px] tracking-widest">
                <template x-if="!updating">
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Git Pull
                    </span>
                </template>
                <template x-if="updating">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memperbarui...
                    </span>
                </template>
            </button>
        </div>
        <div class="p-0 bg-black/50 overflow-hidden">
            <div class="bg-slate-950 p-2 flex items-center gap-2 px-4 border-b border-slate-800">
                <div class="flex gap-1.5">
                    <div class="w-2.5 h-2.5 bg-red-500 rounded-full"></div>
                    <div class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></div>
                    <div class="w-2.5 h-2.5 bg-green-500 rounded-full"></div>
                </div>
                <span class="text-[10px] text-slate-500 font-mono">terminal - bash</span>
            </div>
            <pre class="p-6 text-green-400 font-mono text-xs overflow-x-auto min-h-[200px]" x-text="output || 'Siap untuk diperbarui...'"></pre>
        </div>
    </div>
</div>

<script>
function updateApp() {
    this.updating = true;
    this.output = '$ Running git pull...\n';
    
    fetch('{{ route("settings.git-update") }}')
        .then(response => response.json())
        .then(data => {
            this.output += data.output;
            this.updating = false;
            
            Swal.fire({
                title: 'Update Selesai',
                text: 'Aplikasi telah berhasil ditarik dari GitHub.',
                icon: 'success',
                timer: 3000
            });
        })
        .catch(error => {
            this.output += '\nError: Terjadi kesalahan saat melakukan update.';
            this.updating = false;
            Swal.fire('Error', 'Gagal melakukan pembaruan git.', 'error');
        });
}
</script>
@endsection
