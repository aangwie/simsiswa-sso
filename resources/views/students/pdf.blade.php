<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Siswa - {{ $student->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; line-height: 1.6; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        .section-title { background: #f4f4f4; padding: 5px 10px; font-weight: bold; margin-top: 20px; border-left: 4px solid #4f46e5; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table td { padding: 8px 0; border-bottom: 1px solid #eee; vertical-align: top; }
        table td.label { width: 30%; color: #666; font-size: 12px; text-transform: uppercase; font-weight: bold; }
        table td.value { width: 70%; font-size: 14px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; padding: 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Biodata Siswa</h1>
        <p>Sistem Informasi Manajemen Siswa (SIMSiswa)</p>
    </div>

    <div class="section-title">Informasi Dasar</div>
    <table>
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="value">{{ $student->name }}</td>
        </tr>
        <tr>
            <td class="label">NISN</td>
            <td class="value">{{ $student->nisn ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIS</td>
            <td class="value">{{ $student->nis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td class="value">{{ $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Lahir</td>
            <td class="value">{{ $student->tanggal_lahir ? (\Carbon\Carbon::parse($student->tanggal_lahir)->translatedFormat('d F Y')) : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tahun Masuk</td>
            <td class="value">{{ $student->enrollment_year }}</td>
        </tr>
    </table>

    <div class="section-title">Akademik & Status</div>
    <table>
        <tr>
            <td class="label">Kelas Sekarang</td>
            <td class="value">{{ $student->schoolClass->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tahun Ajaran</td>
            <td class="value">{{ $student->schoolClass->academic_year ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Keaktifan</td>
            <td class="value">{{ $student->is_active ? 'Aktif' : 'Nonaktif' }}</td>
        </tr>
        <tr>
            <td class="label">Status Kelulusan</td>
            <td class="value">{{ $student->status_lulus ? ucfirst($student->status_lulus) : 'Dalam Proses' }}</td>
        </tr>
    </table>

    <div class="section-title">Data Keluarga & Alamat</div>
    <table>
        <tr>
            <td class="label">Nama Ayah</td>
            <td class="value">{{ $student->nama_ayah ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Ibu</td>
            <td class="value">{{ $student->nama_ibu ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Lengkap</td>
            <td class="value">{{ $student->alamat ?? '-' }}</td>
        </tr>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i:s') }} | Dokumen Sah SIMSiswa
    </div>
</body>
</html>
