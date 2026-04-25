<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Setting;
use Illuminate\Support\Facades\Process;

class SettingController extends Controller
{
    public function website()
    {
        $settings = Setting::whereIn('key', ['website_name', 'website_logo'])->get()->pluck('value', 'key');
        $schoolProfile = \Illuminate\Support\Facades\DB::table('school_profiles')->first();
        $kepalaSekolah = \Illuminate\Support\Facades\DB::table('teachers')->where('position', 'Kepala Sekolah')->first();
        return view('settings.website', compact('settings', 'schoolProfile', 'kepalaSekolah'));
    }

    public function websiteUpdate(Request $request)
    {
        $request->validate([
            'website_name' => 'nullable|string|max:255',
            'website_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'nama_sekolah' => 'nullable|string|max:255',
            'alamat_sekolah' => 'nullable|string',
            'kepala_sekolah_name' => 'nullable|string|max:255',
            'kepala_sekolah_nip' => 'nullable|string|max:255',
        ]);

        if ($request->has('website_name')) {
            Setting::updateOrCreate(['key' => 'website_name'], ['value' => $request->website_name]);
        }

        if ($request->hasFile('website_logo')) {
            $file = $request->file('website_logo');
            $base64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));
            Setting::updateOrCreate(['key' => 'website_logo'], ['value' => $base64]);
        }

        $profile = \Illuminate\Support\Facades\DB::table('school_profiles')->first();
        if ($profile) {
            \Illuminate\Support\Facades\DB::table('school_profiles')->where('id', $profile->id)->update([
                'name' => $request->nama_sekolah,
                'address' => $request->alamat_sekolah,
                'updated_at' => now(),
            ]);
        } else {
            \Illuminate\Support\Facades\DB::table('school_profiles')->insert([
                'name' => $request->nama_sekolah,
                'address' => $request->alamat_sekolah,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $kepsek = \Illuminate\Support\Facades\DB::table('teachers')->where('position', 'Kepala Sekolah')->first();
        if ($kepsek) {
            \Illuminate\Support\Facades\DB::table('teachers')->where('id', $kepsek->id)->update([
                'name' => $request->kepala_sekolah_name,
                'nip' => $request->kepala_sekolah_nip,
                'updated_at' => now(),
            ]);
        } else {
            if ($request->kepala_sekolah_name || $request->kepala_sekolah_nip) {
                \Illuminate\Support\Facades\DB::table('teachers')->insert([
                    'name' => $request->kepala_sekolah_name ?? '',
                    'nip' => $request->kepala_sekolah_nip,
                    'position' => 'Kepala Sekolah',
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Pengaturan Website berhasil diperbarui.');
    }

    public function index()
    {
        $settings = Setting::whereNotIn('key', ['website_name', 'website_logo'])->get();
        
        $lastCommit = "";
        try {
            $lastCommit = trim(Process::run("git log -1 --pretty=format:'%h - %an, %ar : %s'")->output());
        } catch (\Exception $e) {
            // Ignore
        }

        $lastUpdateDate = $settings->where('key', 'last_update_date')->first()?->value;
        $lastUpdateVersion = $settings->where('key', 'last_update_version')->first()?->value;

        return view('settings.index', compact('settings', 'lastCommit', 'lastUpdateDate', 'lastUpdateVersion'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', '_method');
        
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function gitUpdate()
    {
        $output = "";
        try {
            $token = Setting::where('key', 'github_token')->first()?->value;

            if (!$token) {
                return response()->json(['output' => "Error: Token GitHub harus diisi di pengaturan."]);
            }

            $repoUrl = 'https://github.com/aangwie/simsiswa-sso.git';
            $branch = trim(Process::run('git rev-parse --abbrev-ref HEAD')->output());

            if (empty($branch)) {
                $branch = 'main';
            }

            // Construct authenticated URL
            $cleanUrl = str_replace(['https://', 'http://'], '', $repoUrl);
            $authUrl = "https://{$token}@{$cleanUrl}";

            $fetchResult = Process::run("git fetch {$authUrl} {$branch}");
            
            $output .= "--- RUNNING: git fetch & force reset [AUTHENTICATED] {$branch} ---\n";
            $maskToken = substr($token, 0, 7) . str_repeat('*', strlen($token) - 7);
            $output .= "Repo: " . str_replace($token, $maskToken, $authUrl) . "\n\n";
            $output .= $fetchResult->output() . $fetchResult->errorOutput() . "\n";

            if ($fetchResult->successful()) {
                $resetResult = Process::run("git reset --hard FETCH_HEAD");
                $cleanResult = Process::run("git clean -fd");
                
                $output .= $resetResult->output() . $resetResult->errorOutput() . "\n";
                $output .= $cleanResult->output() . $cleanResult->errorOutput() . "\n\n";

                $versionResult = Process::run("git log -1 --pretty=format:'%h'");
                Setting::updateOrCreate(['key' => 'last_update_date'], ['value' => now()->toDateTimeString()]);
                Setting::updateOrCreate(['key' => 'last_update_version'], ['value' => trim($versionResult->output())]);
            } else {
                $output .= "Gagal melakukan fetch dari repository.\n\n";
            }

            $logResult = Process::run("git log -1 --pretty=format:'%h - %an, %ar : %s'");
            $output .= "--- LATEST COMMIT ---\n";
            $output .= trim($logResult->output()) . "\n";
            
        } catch (\Exception $e) {
            $output .= "Error: " . $e->getMessage();
        }

        return response()->json(['output' => $output]);
    }
}
