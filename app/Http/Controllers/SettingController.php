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
        return view('settings.website', compact('settings'));
    }

    public function websiteUpdate(Request $request)
    {
        $request->validate([
            'website_name' => 'nullable|string|max:255',
            'website_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($request->has('website_name')) {
            Setting::updateOrCreate(['key' => 'website_name'], ['value' => $request->website_name]);
        }

        if ($request->hasFile('website_logo')) {
            $file = $request->file('website_logo');
            $base64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));
            Setting::updateOrCreate(['key' => 'website_logo'], ['value' => $base64]);
        }

        return redirect()->back()->with('success', 'Pengaturan Website berhasil diperbarui.');
    }

    public function index()
    {
        $settings = Setting::whereNotIn('key', ['website_name', 'website_logo'])->get();
        return view('settings.index', compact('settings'));
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
            $repoUrl = Setting::where('key', 'github_repo_url')->first()?->value;
            $branch = Setting::where('key', 'github_branch')->first()?->value ?? 'main';

            if (!$token || !$repoUrl) {
                return response()->json(['output' => "Error: Token GitHub dan URL Repositori harus diisi di pengaturan."]);
            }

            // Construct authenticated URL
            // repoUrl format usually: https://github.com/user/repo.git
            $cleanUrl = str_replace(['https://', 'http://'], '', $repoUrl);
            $authUrl = "https://{$token}@{$cleanUrl}";

            // Run git pull
            // We use origin as name but point to the auth URL
            $result = Process::run("git pull {$authUrl} {$branch}");
            
            $output .= "--- RUNNING: git pull [AUTHENTICATED] {$branch} ---\n";
            $maskToken = substr($token, 0, 7) . str_repeat('*', strlen($token) - 7);
            $output .= "Repo: " . str_replace($token, $maskToken, $authUrl) . "\n\n";
            $output .= $result->output() . $result->errorOutput() . "\n";
            
        } catch (\Exception $e) {
            $output .= "Error: " . $e->getMessage();
        }

        return response()->json(['output' => $output]);
    }
}
