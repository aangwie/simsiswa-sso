<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>SKL - {{ $student->name }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            padding: 0 5px;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .header-logo-td {
            width: 85px;
            vertical-align: middle;
            text-align: left;
        }

        .header-logo {
            width: 75px;
            height: auto;
        }

        .header-text-td {
            vertical-align: middle;
            text-align: center;
            padding-right: 85px;
        }

        .header-text {
            display: inline-block;
            width: 100%;
        }

        .header-text h1 {
            font-size: 13pt;
            margin: 0;
            text-transform: uppercase;
        }

        .header-text h2 {
            font-size: 15pt;
            margin: 0;
            text-transform: uppercase;
        }

        .header-text p {
            font-size: 8.5pt;
            margin: 2px 0;
        }

        .title {
            text-align: center;
            margin-bottom: 10px;
        }

        .title h3 {
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0;
            font-size: 11.5pt;
        }

        .title p {
            margin: 3px 0 0 0;
            font-size: 9.5pt;
        }

        .content-text {
            margin-bottom: 10px;
            text-align: justify;
        }

        .student-data {
            margin-bottom: 10px;
            margin-left: 15px;
        }

        .student-data table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-data td {
            vertical-align: top;
            padding: 2px 0;
        }

        .status-box {
            text-align: center;
            margin: 10px 0;
        }

        .status-box h4 {
            text-decoration: underline;
            text-transform: uppercase;
            font-size: 12pt;
            margin: 0 0 5px 0;
        }

        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .grades-table th,
        .grades-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: left;
        }

        .grades-table th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 15px;
            width: 100%;
        }

        .signature-section {
            float: right;
            width: 250px;
            text-align: left;
        }

        .stamp-box {
            float: left;
            width: 90px;
            height: 100px;
            border: 1px solid #ccc;
            margin-left: 270px;
            margin-top: 10px;
        }

        .clear {
            clear: both;
        }

        .keterangan {
            font-size: 8.5pt;
            font-style: italic;
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td class="header-logo-td">
                    @if($websiteLogo)
                        <img src="{{ $websiteLogo }}" class="header-logo" alt="Logo">
                    @endif
                </td>
                <td class="header-text-td">
                    <div class="header-text">
                        <h1>Pemerintah Kabupaten Pacitan</h1>
                        <h2>{{ $schoolProfile->name ?? 'SMP Negeri 6 Sudimoro' }}</h2>
                        <p>{{ $schoolProfile->address ?? 'Jl. Raya Pacitan-Trenggalek km. 55, Desa Sukorejo, Kecamatan Sudimoro, Kabupaten Pacitan, Jawa Timur' }}
                        </p>
                        <p>Email: {{ $schoolProfile->email ?? 'smpn6sudimoro@gmail.com' }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <div class="title">
            <h3>Surat Keterangan Lulus Sementara</h3>
            <p>Nomor : {{ $nomorSkl }}</p>
        </div>

        <div class="content-text">
            <p>Yang bertanda tangan dibawah ini, Kepala Sekolah {{ $schoolProfile->name ?? 'SMP Negeri 6 Sudimoro' }}
                Nomor Pokok Sekolah Nasional : 69831500, Kabupaten Pacitan, Provinsi Jawa Timur menerangkan bahwa :</p>
        </div>

        <div class="student-data">
            <table>
                <tr>
                    <td width="180">Nama</td>
                    <td width="10">:</td>
                    <td><strong>{{ strtoupper($student->name) }}</strong></td>
                </tr>
                <tr>
                    <td>Tempat dan tanggal lahir</td>
                    <td>:</td>
                    <td>{{ $student->tempat_lahir ?? '-' }},
                        {{ $student->tanggal_lahir ? \Carbon\Carbon::parse($student->tanggal_lahir)->locale('id')->translatedFormat('d F Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td>Nama orangtua/wali</td>
                    <td>:</td>
                    <td>{{ $student->nama_ayah ?? $student->nama_ibu ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nomor Induk Siswa</td>
                    <td>:</td>
                    <td>{{ $student->nis ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nomor Induk Siswa Nasional</td>
                    <td>:</td>
                    <td>{{ $student->nisn ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="status-box">
            <h4>{{ $isLulus ? 'LULUS' : 'TIDAK LULUS' }}</h4>
            <p>dari {{ $schoolProfile->name ?? 'SMP Negeri 6 Sudimoro' }} pada tanggal
                {{ \Carbon\Carbon::parse($tanggalCetak)->locale('id')->translatedFormat('d F Y') }}, setelah memenuhi
                seluruh kriteria
                sesuai dengan peraturan perundang-undangan, dengan nilai sebagai berikut
            </p>
        </div>

        <table class="grades-table">
            <thead>
                <tr>
                    <th width="40">No.</th>
                    <th>Mata Pelajaran</th>
                    <th width="100">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subjects as $index => $subject)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}.</td>
                        <td>{{ $subject->name }}</td>
                        <td style="text-align: center;">
                            @php
                                $gradeRapor = isset($existingGrades[$subject->id]) ? floatval($existingGrades[$subject->id]->grade) : 0;
                                $gradeUsp = isset($uspGrades[$subject->id]) ? floatval($uspGrades[$subject->id]->grade) : 0;
                                $rataAkhir = ($gradeRapor + $gradeUsp) / 2;
                            @endphp
                            {{ number_format($rataAkhir, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="text-center"><strong>Rata-rata</strong></td>
                    <td style="text-align: center;"><strong>{{ number_format($average, 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="keterangan">
            <strong>Keterangan :</strong><br>
            Surat Keterangan Lulus Sementara ini dapat dipergunakan untuk keperluan melanjutkan pendidikan ke jenjang
            SMA/MA/SMK/Sederajat atau keperluan lain sesuai dengan kebutuhan dan hanya berlaku sampai diterbitkannya
            Ijazah Tahun Pelajaran {{ date('Y') }}/{{ date('Y') + 1 }}.
        </div>

        <div class="footer">
            <div class="stamp-box">
                <!-- Box for Photo/Stamp as in the image -->
            </div>
            <div class="signature-section">
                {{ $tempatCetak }},
                {{ \Carbon\Carbon::parse($tanggalCetak)->locale('id')->translatedFormat('d F Y') }}<br>
                Kepala Sekolah,<br><br><br><br>
                <strong><u>{{ $kepalaSekolah->name ?? 'Drs. MARJOKO, M.MPd' }}</u></strong><br>
                NIP. {{ $kepalaSekolah->nip ?? '19680916 199903 1 010' }}
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>

</html>