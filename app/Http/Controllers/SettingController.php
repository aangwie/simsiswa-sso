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

            $projectPath = base_path();
            $repoUrl = 'https://github.com/aangwie/simsiswa-sso.git';
            $branch = 'main';
            $authUrl = "https://{$token}@github.com/aangwie/simsiswa-sso.git";
            $maskToken = substr($token, 0, 7) . str_repeat('*', strlen($token) - 7);
            $maskedUrl = "https://{$maskToken}@github.com/aangwie/simsiswa-sso.git";

            $output .= "--- PROJECT PATH: {$projectPath} ---\n";
            $output .= "--- REPO: {$maskedUrl} ---\n";
            $output .= "--- BRANCH: {$branch} ---\n\n";

            // Try to detect current branch
            $branchDetect = $this->runGitCommand('git rev-parse --abbrev-ref HEAD', $projectPath);
            if (!empty(trim($branchDetect))) {
                $branch = trim($branchDetect);
                $output .= "Detected branch: {$branch}\n";
            }

            // Step 1: git fetch
            $output .= "\n\$ git fetch [AUTHENTICATED] {$branch}\n";
            $fetchOutput = $this->runGitCommand("git fetch {$authUrl} {$branch}", $projectPath);
            $output .= str_replace($token, $maskToken, $fetchOutput) . "\n";

            // Step 2: git reset --hard FETCH_HEAD
            $output .= "\$ git reset --hard FETCH_HEAD\n";
            $resetOutput = $this->runGitCommand("git reset --hard FETCH_HEAD", $projectPath);
            $output .= $resetOutput . "\n";

            // Step 3: git clean -fd
            $output .= "\$ git clean -fd\n";
            $cleanOutput = $this->runGitCommand("git clean -fd", $projectPath);
            $output .= $cleanOutput . "\n\n";

            // Get latest commit info
            $commitHash = trim($this->runGitCommand("git log -1 --pretty=format:%h", $projectPath));
            $commitLog = trim($this->runGitCommand("git log -1 --pretty=format:%h - %an, %ar : %s", $projectPath));

            if (!empty($commitHash)) {
                Setting::updateOrCreate(['key' => 'last_update_date'], ['value' => now()->toDateTimeString()]);
                Setting::updateOrCreate(['key' => 'last_update_version'], ['value' => $commitHash]);
            }

            $output .= "--- LATEST COMMIT ---\n";
            $output .= $commitLog . "\n";
            
        } catch (\Exception $e) {
            $output .= "\nException: " . $e->getMessage() . "\n";
            $output .= "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }

        return response()->json(['output' => $output]);
    }

    /**
     * Run a git command with proper working directory.
     * Falls back to exec() if Process facade fails.
     */
    private function runGitCommand(string $command, string $cwd): string
    {
        // Method 1: Laravel Process facade with explicit path
        try {
            $result = Process::path($cwd)->run($command);
            $out = $result->output() . $result->errorOutput();
            if (!empty(trim($out))) {
                return $out;
            }
        } catch (\Exception $e) {
            // Fall through to exec
        }

        // Method 2: exec() with cd
        try {
            $fullCommand = "cd " . escapeshellarg($cwd) . " && {$command} 2>&1";
            $execOutput = '';
            exec($fullCommand, $lines, $exitCode);
            $execOutput = implode("\n", $lines);
            return $execOutput;
        } catch (\Exception $e) {
            // Fall through to shell_exec
        }

        // Method 3: shell_exec
        try {
            $fullCommand = "cd " . escapeshellarg($cwd) . " && {$command} 2>&1";
            $shellOutput = shell_exec($fullCommand);
            return $shellOutput ?? '';
        } catch (\Exception $e) {
            return "Error executing command: " . $e->getMessage();
        }
    }
}
