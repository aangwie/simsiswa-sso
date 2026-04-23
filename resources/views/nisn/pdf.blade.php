<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu NISN - {{ $student->name }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 20px;
        }
        .card {
            width: 323px; /* 8.56 cm */
            height: 204px; /* 5.39 cm */
            display: inline-block;
            margin-right: 15px;
            border: 1px solid #999;
            border-radius: 8px;
            overflow: hidden;
            background-color: #d8f0f8; /* Light blue */
            vertical-align: top;
            box-sizing: border-box;
            position: relative;
        }

        /* Front Card */
        .front-header {
            width: 100%;
            padding: 5px 8px;
            box-sizing: border-box;
        }
        .logo-kemdikbud {
            float: left;
            width: 150px;
        }
        .logo-img {
            float: left;
            height: 25px;
            margin-right: 5px;
        }
        .logo-text {
            float: left;
            margin-top: 2px;
            line-height: 1;
        }
        .text-blue { color: #00a8e8; font-weight: bold; font-size: 11px; }
        .text-orange { color: #f39c12; font-weight: bold; font-size: 11px; }
        .text-small { font-size: 5px; color: #555; display: block; margin-top: 1px; }

        .title-box {
            float: right;
            text-align: right;
            line-height: 1.1;
        }
        .title-main { color: #2980b9; font-size: 13px; font-weight: bold; margin: 0; }
        .title-sub { color: #444; font-size: 7px; margin: 0; }
        
        .clear { clear: both; }

        .front-content {
            width: 100%;
            padding: 5px 10px;
            box-sizing: border-box;
        }

        .student-photo {
            float: left;
            width: 55px;
            height: 75px;
            background: #fff;
            border: 1px solid #ccc;
            text-align: center;
            box-sizing: border-box;
            padding-top: 15px;
        }
        .student-photo img { width: 35px; }
        .student-photo .nisn-label { font-size: 8px; font-weight: bold; color: #2980b9; margin-top: 5px; }

        .student-data {
            float: left;
            width: 170px;
            margin-left: 10px;
        }
        .student-data table {
            width: 100%;
            font-size: 9px;
            border-collapse: collapse;
            color: #111;
        }
        .student-data td { padding: 2px 0; vertical-align: top; }
        .col-label { width: 65px; }

        .qr-box {
            float: right;
            width: 50px;
            height: 50px;
            background: #fff;
            padding: 2px;
            box-sizing: border-box;
            margin-top: 20px;
            border: 1px solid #ccc;
        }

        .front-footer {
            position: absolute;
            bottom: 5px;
            left: 10px;
            line-height: 1.1;
        }

        /* Back Card */
        .back-top {
            text-align: center;
            padding-top: 15px;
            line-height: 1.1;
        }
        .back-top img { height: 40px; margin-bottom: 3px; }
        .back-top .text-small { font-size: 6px; }
        
        .back-bar {
            background-color: #6a7f7c;
            color: #fff;
            text-align: center;
            padding: 6px 0;
            margin-top: 15px;
            line-height: 1.2;
        }
        .back-bar-main { font-size: 13px; font-weight: bold; margin: 0; letter-spacing: 1px; }
        .back-bar-sub { font-size: 7px; margin: 0; }

        .back-links {
            text-align: center;
            font-size: 8px;
            color: #444;
            margin-top: 12px;
            line-height: 1.3;
        }
        
        .back-footer {
            text-align: center;
            margin-top: 15px;
            line-height: 1.1;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- FRONT CARD -->
        <div class="card">
            <!-- Header -->
            <div class="front-header">
                <div class="logo-kemdikbud">
                    <img src="{{ public_path('images/Tutwuri.webp') }}" class="logo-img">
                    <div class="logo-text">
                        <span class="text-blue">Kemen</span><span class="text-orange">dikdasmen</span>
                        <span class="text-small">Kementerian Pendidikan Dasar dan Menengah</span>
                    </div>
                </div>
                <div class="title-box">
                    <p class="title-main">KARTU NISN</p>
                    <p class="title-sub">NOMOR INDUK SISWA NASIONAL</p>
                </div>
                <div class="clear"></div>
            </div>

            <!-- Content -->
            <div class="front-content">
                <div class="student-photo">
                    <img src="{{ public_path('images/Tutwuri.webp') }}">
                    <div class="nisn-label">NISN</div>
                </div>

                <div class="student-data">
                    <table>
                        <tr>
                            <td class="col-label">NISN</td>
                            <td>: <b>{{ $student->nisn }}</b></td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>: {{ substr($student->name, 0, 25) }}</td>
                        </tr>
                        <tr>
                            <td>Tempat Lahir</td>
                            <td>: {{ $student->tempat_lahir }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Lahir</td>
                            <td>: {{ \Carbon\Carbon::parse($student->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td>Jenis Kelamin</td>
                            <td>: {{ strtolower($student->gender) === 'female' ? 'Perempuan' : 'Laki-Laki' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="qr-box">
                    @php
                        // Using quickchart.io for reliable QR code generation in DomPDF
                        $qrUrl = 'https://quickchart.io/qr?text=' . urlencode($student->nisn) . '&size=100&margin=0';
                    @endphp
                    <img src="{{ $qrUrl }}" style="width: 100%; height: 100%;">
                </div>
                
                <div class="clear"></div>
            </div>

            <!-- Footer -->
            <div class="front-footer">
                <span class="text-blue" style="font-size: 12px; letter-spacing: 0.5px;">DAPODIK</span><br>
                <span class="text-small">DATA POKOK PENDIDIKAN</span>
            </div>
        </div>

        <!-- BACK CARD -->
        <div class="card">
            <div class="back-top">
                <img src="{{ public_path('images/Tutwuri.webp') }}"><br>
                <span class="text-blue" style="font-size:13px;">Kemen</span><span class="text-orange" style="font-size:13px;">dikdasmen</span><br>
                <span class="text-small" style="font-size:7px;">Pusat Data dan Teknologi Informasi</span>
            </div>

            <div class="back-bar">
                <p class="back-bar-main">KARTU NISN</p>
                <p class="back-bar-sub">NOMOR INDUK SISWA NASIONAL</p>
            </div>

            <div class="back-links">
                Link Resmi NISN :<br>
                https://nisn.data.kemdikbud.go.id<br><br>
                didukung oleh :<br>
                {{ \App\Models\Setting::where('key', 'website_name')->first()?->value ?? 'SIMSiswa' }}
            </div>

            <div class="back-footer">
                <span class="text-blue" style="font-size: 13px; letter-spacing: 0.5px;">DAPODIK</span><br>
                <span class="text-small" style="font-size: 6px;">DATA POKOK PENDIDIKAN</span>
                <span class="text-small" style="font-size: 6px;">dapo.kemdikbud.go.id</span>
            </div>
        </div>
    </div>

</body>
</html>
