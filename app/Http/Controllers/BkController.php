<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\BkKonsultasi;
use App\Models\BkJadwalKonsultasi;
use App\Models\BkPelanggaran;
use App\Models\BkPoinSiswa;
use App\Models\BkSolusi;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class BkController extends Controller
{
    // ========================
    // DASHBOARD
    // ========================
    public function dashboard()
    {
        // Siswa bermasalah (poin > 20)
        $siswaBermasalah = DB::table('bk_poin_siswas')
            ->join('bk_pelanggarans', 'bk_poin_siswas.bk_pelanggaran_id', '=', 'bk_pelanggarans.id')
            ->select('bk_poin_siswas.student_id', DB::raw('SUM(bk_pelanggarans.poin) as total_poin'))
            ->groupBy('bk_poin_siswas.student_id')
            ->having('total_poin', '>', 20)
            ->count();

        // Konsultasi hari ini
        $konsultasiHariIni = BkKonsultasi::whereDate('tanggal_pengajuan', today())->count();

        // Total konsultasi
        $totalKonsultasi = BkKonsultasi::count();

        // Total pelanggaran tercatat
        $totalPelanggaran = BkPoinSiswa::count();

        // Top 5 pelanggaran terbanyak
        $topPelanggaran = DB::table('bk_poin_siswas')
            ->join('bk_pelanggarans', 'bk_poin_siswas.bk_pelanggaran_id', '=', 'bk_pelanggarans.id')
            ->select('bk_pelanggarans.nama_pelanggaran', 'bk_pelanggarans.poin', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('bk_pelanggarans.id', 'bk_pelanggarans.nama_pelanggaran', 'bk_pelanggarans.poin')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        // Siswa dengan poin tertinggi
        $topSiswa = DB::table('bk_poin_siswas')
            ->join('bk_pelanggarans', 'bk_poin_siswas.bk_pelanggaran_id', '=', 'bk_pelanggarans.id')
            ->join('students', 'bk_poin_siswas.student_id', '=', 'students.id')
            ->leftJoin('school_classes', 'students.school_class_id', '=', 'school_classes.id')
            ->select('students.id', 'students.name', 'school_classes.name as kelas', DB::raw('SUM(bk_pelanggarans.poin) as total_poin'))
            ->groupBy('students.id', 'students.name', 'school_classes.name')
            ->orderByDesc('total_poin')
            ->limit(5)
            ->get();

        return view('bk.dashboard', compact(
            'siswaBermasalah', 'konsultasiHariIni', 'totalKonsultasi', 'totalPelanggaran',
            'topPelanggaran', 'topSiswa'
        ));
    }

    // ========================
    // KONSULTASI
    // ========================
    public function konsultasiIndex(Request $request)
    {
        $query = BkKonsultasi::with(['student.schoolClass', 'jadwal', 'solusi']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pengajuan', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pengajuan', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $konsultasi = $query->orderByDesc('tanggal_pengajuan')->paginate(15)->withQueryString();
        $students = Student::orderBy('name')->get();

        return view('bk.konsultasi', compact('konsultasi', 'students'));
    }

    public function konsultasiStore(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'jenis_masalah' => 'required|in:pribadi,akademik,sosial,disiplin',
            'deskripsi' => 'nullable|string',
        ]);

        $validated['tanggal_pengajuan'] = now()->toDateString();
        $validated['status'] = 'pending';

        BkKonsultasi::create($validated);

        return redirect()->back()->with('success', 'Konsultasi berhasil diajukan.');
    }

    public function konsultasiUpdate(Request $request, $id)
    {
        $konsultasi = BkKonsultasi::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'jenis_masalah' => 'required|in:pribadi,akademik,sosial,disiplin',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:pending,dijadwalkan,selesai',
        ]);

        $konsultasi->update($validated);

        return redirect()->back()->with('success', 'Konsultasi berhasil diperbarui.');
    }

    public function konsultasiDestroy($id)
    {
        BkKonsultasi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Konsultasi berhasil dihapus.');
    }

    // ========================
    // JADWAL KONSULTASI
    // ========================
    public function jadwalStore(Request $request)
    {
        $validated = $request->validate([
            'bk_konsultasi_id' => 'required|exists:bk_konsultasis,id',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'guru_bk' => 'nullable|string|max:255',
            'ruang' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        BkJadwalKonsultasi::updateOrCreate(
            ['bk_konsultasi_id' => $validated['bk_konsultasi_id']],
            $validated
        );

        // Update status konsultasi
        BkKonsultasi::where('id', $validated['bk_konsultasi_id'])->update(['status' => 'dijadwalkan']);

        return redirect()->back()->with('success', 'Jadwal konsultasi berhasil disimpan.');
    }

    public function jadwalUpdate(Request $request, $id)
    {
        $jadwal = BkJadwalKonsultasi::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jam' => 'required',
            'guru_bk' => 'nullable|string|max:255',
            'ruang' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        $jadwal->update($validated);

        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    // ========================
    // MASTER PELANGGARAN
    // ========================
    public function pelanggaranIndex()
    {
        $pelanggarans = BkPelanggaran::withCount('poinSiswa')->orderBy('nama_pelanggaran')->paginate(15);
        return view('bk.pelanggaran', compact('pelanggarans'));
    }

    public function pelanggaranStore(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:1',
        ]);

        BkPelanggaran::create($validated);

        return redirect()->back()->with('success', 'Data pelanggaran berhasil ditambahkan.');
    }

    public function pelanggaranUpdate(Request $request, $id)
    {
        $pelanggaran = BkPelanggaran::findOrFail($id);

        $validated = $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:1',
        ]);

        $pelanggaran->update($validated);

        return redirect()->back()->with('success', 'Data pelanggaran berhasil diperbarui.');
    }

    public function pelanggaranDestroy($id)
    {
        BkPelanggaran::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data pelanggaran berhasil dihapus.');
    }

    // ========================
    // POIN SISWA
    // ========================
    public function poinIndex(Request $request)
    {
        $query = DB::table('bk_poin_siswas')
            ->join('bk_pelanggarans', 'bk_poin_siswas.bk_pelanggaran_id', '=', 'bk_pelanggarans.id')
            ->join('students', 'bk_poin_siswas.student_id', '=', 'students.id')
            ->leftJoin('school_classes', 'students.school_class_id', '=', 'school_classes.id')
            ->select(
                'students.id',
                'students.name',
                'school_classes.name as kelas',
                DB::raw('SUM(bk_pelanggarans.poin) as total_poin'),
                DB::raw('COUNT(bk_poin_siswas.id) as jumlah_pelanggaran')
            )
            ->groupBy('students.id', 'students.name', 'school_classes.name');

        if ($request->filled('search')) {
            $query->where('students.name', 'like', '%' . $request->search . '%');
        }

        $poinSiswa = $query->orderByDesc('total_poin')->paginate(15)->withQueryString();

        $students = Student::orderBy('name')->get();
        $pelanggarans = BkPelanggaran::orderBy('nama_pelanggaran')->get();

        // Detail pelanggaran (for modal)
        $detailPoin = BkPoinSiswa::with(['student', 'pelanggaran'])->orderByDesc('tanggal')->get();

        return view('bk.poin', compact('poinSiswa', 'students', 'pelanggarans', 'detailPoin'));
    }

    public function poinStore(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'bk_pelanggaran_id' => 'required|exists:bk_pelanggarans,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        BkPoinSiswa::create($validated);

        return redirect()->back()->with('success', 'Poin pelanggaran berhasil dicatat.');
    }

    public function poinDestroy($id)
    {
        BkPoinSiswa::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data poin berhasil dihapus.');
    }

    // ========================
    // SOLUSI
    // ========================
    public function solusiStore(Request $request)
    {
        $validated = $request->validate([
            'bk_konsultasi_id' => 'required|exists:bk_konsultasis,id',
            'solusi' => 'nullable|string',
            'tindakan' => 'required|in:konseling_lanjutan,panggilan_orang_tua,surat_peringatan',
        ]);

        $validated['status'] = 'pending';

        BkSolusi::updateOrCreate(
            ['bk_konsultasi_id' => $validated['bk_konsultasi_id']],
            $validated
        );

        return redirect()->back()->with('success', 'Solusi berhasil disimpan.');
    }

    public function solusiUpdate(Request $request, $id)
    {
        $solusi = BkSolusi::findOrFail($id);

        $validated = $request->validate([
            'solusi' => 'nullable|string',
            'tindakan' => 'required|in:konseling_lanjutan,panggilan_orang_tua,surat_peringatan',
            'status' => 'required|in:pending,selesai',
        ]);

        $solusi->update($validated);

        // If solusi selesai, mark konsultasi selesai too
        if ($validated['status'] === 'selesai') {
            $solusi->konsultasi()->update(['status' => 'selesai']);
        }

        return redirect()->back()->with('success', 'Solusi berhasil diperbarui.');
    }

    // ========================
    // RIWAYAT SISWA
    // ========================
    public function riwayat($studentId)
    {
        $student = Student::with('schoolClass')->findOrFail($studentId);

        $riwayatPelanggaran = BkPoinSiswa::with('pelanggaran')
            ->where('student_id', $studentId)
            ->orderByDesc('tanggal')
            ->get();

        $totalPoin = $riwayatPelanggaran->sum(fn($p) => $p->pelanggaran->poin ?? 0);

        $riwayatKonsultasi = BkKonsultasi::with(['jadwal', 'solusi'])
            ->where('student_id', $studentId)
            ->orderByDesc('tanggal_pengajuan')
            ->get();

        return view('bk.riwayat', compact('student', 'riwayatPelanggaran', 'totalPoin', 'riwayatKonsultasi'));
    }

    // ========================
    // LAPORAN
    // ========================
    public function laporanIndex(Request $request)
    {
        $students = Student::orderBy('name')->get();
        $pelanggarans = BkPelanggaran::orderBy('nama_pelanggaran')->get();

        $data = collect();

        if ($request->filled('student_id') || $request->filled('bulan') || $request->filled('pelanggaran_id')) {
            $query = BkPoinSiswa::with(['student.schoolClass', 'pelanggaran']);

            if ($request->filled('student_id')) {
                $query->where('student_id', $request->student_id);
            }
            if ($request->filled('bulan')) {
                $query->whereMonth('tanggal', date('m', strtotime($request->bulan)))
                      ->whereYear('tanggal', date('Y', strtotime($request->bulan)));
            }
            if ($request->filled('pelanggaran_id')) {
                $query->where('bk_pelanggaran_id', $request->pelanggaran_id);
            }

            $data = $query->orderByDesc('tanggal')->get();
        }

        return view('bk.laporan', compact('students', 'pelanggarans', 'data'));
    }

    public function laporanExportPdf(Request $request)
    {
        $query = BkPoinSiswa::with(['student.schoolClass', 'pelanggaran']);

        $filterLabel = 'Semua Data';

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
            $student = Student::find($request->student_id);
            $filterLabel = 'Siswa: ' . ($student->name ?? '');
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', date('m', strtotime($request->bulan)))
                  ->whereYear('tanggal', date('Y', strtotime($request->bulan)));
            $filterLabel .= ' | Bulan: ' . date('F Y', strtotime($request->bulan));
        }
        if ($request->filled('pelanggaran_id')) {
            $query->where('bk_pelanggaran_id', $request->pelanggaran_id);
            $pelanggaran = BkPelanggaran::find($request->pelanggaran_id);
            $filterLabel .= ' | Pelanggaran: ' . ($pelanggaran->nama_pelanggaran ?? '');
        }

        $data = $query->orderByDesc('tanggal')->get();

        $schoolProfile = DB::table('school_profiles')->first();

        $pdf = Pdf::loadView('bk.laporan_pdf', compact('data', 'filterLabel', 'schoolProfile'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_BK_' . date('Ymd_His') . '.pdf');
    }
}
