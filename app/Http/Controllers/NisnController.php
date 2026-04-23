<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class NisnController extends Controller
{
    /**
     * Halaman form pencarian NISN
     */
    public function index()
    {
        return view('nisn.index');
    }

    /**
     * Verifikasi NISN + tanggal lahir, tampilkan hasil
     */
    public function check(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
            'tanggal_lahir' => 'required|date_format:d/m/Y',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date_format' => 'Format tanggal lahir harus dd/mm/yyyy.',
        ]);

        $nisn = $request->nisn;
        $tanggalLahir = Carbon::createFromFormat('d/m/Y', $request->tanggal_lahir)->format('Y-m-d');

        // Cari siswa berdasarkan NISN dan Tanggal Lahir
        $student = Student::where('nisn', $nisn)
            ->where('tanggal_lahir', $tanggalLahir)
            ->first();

        if (!$student) {
            return redirect()->route('cetak-nisn.index')
                ->withErrors(['nisn' => 'Data siswa tidak ditemukan. Pastikan NISN dan tanggal lahir sudah benar.'])
                ->withInput();
        }

        return view('nisn.result', compact('student'));
    }

    /**
     * Generate PDF Kartu NISN
     */
    public function pdf(Student $student)
    {
        $websiteName = Setting::where('key', 'website_name')->first()?->value ?? 'SIMSiswa';
        $websiteLogo = Setting::where('key', 'website_logo')->first()?->value;
        $pdf = Pdf::setOptions(['isRemoteEnabled' => true])
            ->loadView('nisn.pdf', compact('student', 'websiteName', 'websiteLogo'));
        
        // Custom paper size for ID card (landscape)
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Kartu_NISN_' . $student->nisn . '_' . $student->name . '.pdf');
    }
}
