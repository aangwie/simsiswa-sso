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
        $hasGit = is_dir(base_path('.git'));
        
        if ($hasGit) {
            try {
                $lastCommit = trim(Process::path(base_path())->run("git log -1 --pretty=format:%h - %an, %ar : %s")->output());
            } catch (\Exception $e) {
                // Ignore
            }
        }
        
        // Fallback to saved version from DB
        if (empty($lastCommit)) {
            $savedVersion = $settings->where('key', 'last_update_version')->first()?->value;
            if ($savedVersion) {
                $lastCommit = "Versi tersimpan: {$savedVersion}";
            } else {
                $lastCommit = "Belum ada info commit.";
            }
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
            $branch = 'main';
            $hasGit = is_dir($projectPath . DIRECTORY_SEPARATOR . '.git');

            $output .= "--- PROJECT PATH: {$projectPath} ---\n";
            $output .= "--- BRANCH: {$branch} ---\n";
            $output .= "--- MODE: " . ($hasGit ? 'GIT' : 'API (no .git found)') . " ---\n\n";

            if ($hasGit) {
                $output .= $this->gitPullViaGit($token, $branch, $projectPath);
            } else {
                $output .= $this->gitPullViaApi($token, $branch, $projectPath);
            }

        } catch (\Exception $e) {
            $output .= "\nException: " . $e->getMessage() . "\n";
            $output .= "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }

        return response()->json(['output' => $output]);
    }

    /**
     * Update via git commands (when .git directory exists)
     */
    private function gitPullViaGit(string $token, string $branch, string $projectPath): string
    {
        $output = "";
        $authUrl = "https://{$token}@github.com/aangwie/simsiswa-sso.git";
        $maskToken = substr($token, 0, 7) . str_repeat('*', strlen($token) - 7);

        // Detect branch
        $branchDetect = $this->runGitCommand('git rev-parse --abbrev-ref HEAD', $projectPath);
        if (!empty(trim($branchDetect)) && !str_contains($branchDetect, 'fatal')) {
            $branch = trim($branchDetect);
        }
        $output .= "Detected branch: {$branch}\n\n";

        // Fetch
        $output .= "\$ git fetch {$branch}\n";
        $fetchOutput = $this->runGitCommand("git fetch {$authUrl} {$branch}", $projectPath);
        $output .= str_replace($token, $maskToken, $fetchOutput) . "\n";

        // Reset
        $output .= "\$ git reset --hard FETCH_HEAD\n";
        $output .= $this->runGitCommand("git reset --hard FETCH_HEAD", $projectPath) . "\n";

        // Clean
        $output .= "\$ git clean -fd\n";
        $output .= $this->runGitCommand("git clean -fd", $projectPath) . "\n\n";

        // Commit info
        $commitHash = trim($this->runGitCommand("git log -1 --pretty=format:%h", $projectPath));
        $commitLog = trim($this->runGitCommand("git log -1 --pretty=format:%h - %an, %ar : %s", $projectPath));

        if (!empty($commitHash) && !str_contains($commitHash, 'fatal')) {
            Setting::updateOrCreate(['key' => 'last_update_date'], ['value' => now()->toDateTimeString()]);
            Setting::updateOrCreate(['key' => 'last_update_version'], ['value' => $commitHash]);
        }

        $output .= "--- LATEST COMMIT ---\n";
        $output .= $commitLog . "\n";

        return $output;
    }

    /**
     * Update via GitHub API (when .git directory does NOT exist, e.g. shared hosting)
     * Downloads ZIP from GitHub, extracts and overwrites project files.
     */
    private function gitPullViaApi(string $token, string $branch, string $projectPath): string
    {
        $output = "";
        $owner = 'aangwie';
        $repo = 'simsiswa-sso';

        // Step 1: Get latest commit info from API
        $output .= "Fetching latest commit info from GitHub API...\n";
        $commitApiUrl = "https://api.github.com/repos/{$owner}/{$repo}/commits/{$branch}";
        $commitData = $this->githubApiRequest($commitApiUrl, $token);

        if (!$commitData || !isset($commitData['sha'])) {
            $output .= "Error: Gagal mengambil info commit dari GitHub API.\n";
            if (is_array($commitData) && isset($commitData['message'])) {
                $output .= "GitHub API: " . $commitData['message'] . "\n";
            }
            return $output;
        }

        $commitSha = substr($commitData['sha'], 0, 7);
        $commitFullSha = $commitData['sha'];
        $commitMessage = $commitData['commit']['message'] ?? 'N/A';
        $commitAuthor = $commitData['commit']['author']['name'] ?? 'N/A';
        $commitDate = $commitData['commit']['author']['date'] ?? 'N/A';

        $output .= "Latest commit: {$commitSha} - {$commitAuthor} : {$commitMessage}\n\n";

        // Step 2: Download ZIP
        $output .= "Downloading repository ZIP from GitHub...\n";
        $zipUrl = "https://api.github.com/repos/{$owner}/{$repo}/zipball/{$branch}";
        $tempZip = $projectPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . '_update.zip';
        $tempExtract = $projectPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . '_update_extract';

        $downloaded = $this->downloadFile($zipUrl, $tempZip, $token);
        if (!$downloaded) {
            $output .= "Error: Gagal mendownload ZIP dari GitHub.\n";
            return $output;
        }

        $fileSize = round(filesize($tempZip) / 1024, 1);
        $output .= "Downloaded: {$fileSize} KB\n\n";

        // Step 3: Extract ZIP
        $output .= "Extracting ZIP...\n";
        $zip = new \ZipArchive();
        if ($zip->open($tempZip) !== true) {
            $output .= "Error: Gagal membuka file ZIP.\n";
            @unlink($tempZip);
            return $output;
        }

        // Clean temp extract dir
        if (is_dir($tempExtract)) {
            $this->deleteDirectory($tempExtract);
        }
        mkdir($tempExtract, 0755, true);

        $zip->extractTo($tempExtract);
        $zip->close();

        // Find the extracted root folder (GitHub adds a prefix like "owner-repo-hash/")
        $extractedDirs = glob($tempExtract . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
        if (empty($extractedDirs)) {
            $output .= "Error: Folder hasil ekstraksi tidak ditemukan.\n";
            @unlink($tempZip);
            $this->deleteDirectory($tempExtract);
            return $output;
        }
        $sourceDir = $extractedDirs[0];

        // Step 4: Copy files to project (skip protected directories)
        $output .= "Updating project files...\n";
        $skipDirs = ['.git', 'vendor', 'node_modules', 'storage', '.env'];
        $updatedCount = $this->copyDirectory($sourceDir, $projectPath, $skipDirs);
        $output .= "Updated {$updatedCount} files.\n\n";

        // Step 5: Cleanup
        @unlink($tempZip);
        $this->deleteDirectory($tempExtract);
        $output .= "Cleanup completed.\n\n";

        // Step 6: Save version info
        Setting::updateOrCreate(['key' => 'last_update_date'], ['value' => now()->toDateTimeString()]);
        Setting::updateOrCreate(['key' => 'last_update_version'], ['value' => $commitSha]);

        $output .= "--- LATEST COMMIT ---\n";
        $output .= "{$commitSha} - {$commitAuthor} : {$commitMessage}\n";

        return $output;
    }

    /**
     * Make a GET request to GitHub API
     */
    private function githubApiRequest(string $url, string $token): ?array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$token}",
                "Accept: application/vnd.github.v3+json",
                "User-Agent: SIMSiswa-Updater",
            ],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode($response, true) : null;
    }

    /**
     * Download a file from URL (follows redirects, supports GitHub auth)
     */
    private function downloadFile(string $url, string $destination, string $token): bool
    {
        $ch = curl_init();
        $fp = fopen($destination, 'w+');

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_FILE => $fp,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$token}",
                "Accept: application/vnd.github.v3+json",
                "User-Agent: SIMSiswa-Updater",
            ],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $success = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);

        if (!$success || $httpCode !== 200) {
            @unlink($destination);
            return false;
        }

        return true;
    }

    /**
     * Recursively copy directory, skipping protected folders
     */
    private function copyDirectory(string $source, string $dest, array $skipItems = []): int
    {
        $count = 0;
        $dir = opendir($source);

        while (($item = readdir($dir)) !== false) {
            if ($item === '.' || $item === '..') continue;
            if (in_array($item, $skipItems)) continue;

            $srcPath = $source . DIRECTORY_SEPARATOR . $item;
            $dstPath = $dest . DIRECTORY_SEPARATOR . $item;

            if (is_dir($srcPath)) {
                if (!is_dir($dstPath)) {
                    mkdir($dstPath, 0755, true);
                }
                $count += $this->copyDirectory($srcPath, $dstPath, []);
            } else {
                copy($srcPath, $dstPath);
                $count++;
            }
        }
        closedir($dir);

        return $count;
    }

    /**
     * Recursively delete a directory
     */
    private function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) return false;

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        return rmdir($dir);
    }

    /**
     * Run a git command with proper working directory.
     * Falls back to exec() if Process facade fails.
     */
    private function runGitCommand(string $command, string $cwd): string
    {
        try {
            $result = Process::path($cwd)->run($command);
            $out = $result->output() . $result->errorOutput();
            if (!empty(trim($out))) {
                return $out;
            }
        } catch (\Exception $e) {
            // Fall through
        }

        try {
            $fullCommand = "cd " . escapeshellarg($cwd) . " && {$command} 2>&1";
            exec($fullCommand, $lines, $exitCode);
            return implode("\n", $lines);
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
