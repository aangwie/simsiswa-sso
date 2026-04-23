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
            padding: 10px;
        }
        .card {
            width: 11.5cm;
            height: 6.9cm;
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 20px;
            border: 1px solid #999;
            border-radius: 9px;
            overflow: hidden;
            background: #e0f7fa; /* Fallback for wkhtmltopdf */
            background: -webkit-linear-gradient(top left, #e0f7fa 0%, #b3e5fc 40%, #ffffff 100%);
            background: linear-gradient(135deg, #e0f7fa 0%, #b3e5fc 40%, #ffffff 100%);
            vertical-align: top;
            box-sizing: border-box;
            position: relative;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: 0;
            pointer-events: none;
        }
        .watermark img {
            width: 200px;
        }

        /* Front Card */
        .front-header {
            width: 100%;
            padding: 6px 9px;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
        }
        .logo-kemdikbud {
            float: left;
            width: 172px;
        }
        .logo-img {
            float: left;
            height: 29px;
            margin-right: 6px;
        }
        .logo-text {
            float: left;
            margin-top: 3px;
            line-height: 1;
        }
        .text-blue { color: #00a8e8; font-weight: bold; font-size: 13px; }
        .text-orange { color: #f39c12; font-weight: bold; font-size: 13px; }
        .text-small { font-size: 6px; color: #555; display: block; margin-top: 2px; }

        .title-box {
            float: right;
            text-align: center;
            line-height: 1.1;
        }
        .title-main { color: #2980b9; font-size: 15px; font-weight: bold; margin: 0; }
        .title-sub { color: #444; font-size: 8px; margin: 0; }
        
        .clear { clear: both; }

        .front-content {
            width: 100%;
            padding: 6px 11px;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
        }

        .left-section {
            float: left;
            width: 86px;
            text-align: center;
        }
        .nisn-logo {
            width: 52px;
            margin-bottom: 3px;
        }
        .user-photo {
            width: 86px;
            height: 109px;
            border: 1px solid #ccc;
            background: #fff;
            padding: 2px;
            box-sizing: border-box;
            object-fit: cover;
            opacity: 0.5;
        }

        .student-data {
            float: left;
            width: 247px;
            margin-left: 17px;
            margin-top: 29px;
        }
        .student-data table {
            width: 100%;
            font-size: 11px;
            border-collapse: collapse;
            color: #111;
        }
        .student-data td { padding: 3px 0; vertical-align: top; }
        .col-label { width: 86px; }

        .qr-bottom-right {
            position: absolute;
            bottom: 12px;
            right: 12px;
            width: 57px;
            height: 57px;
            background: #fff;
            padding: 2px;
            box-sizing: border-box;
            border: 1px solid #ccc;
        }
        .qr-bottom-right img { width: 100%; height: 100%; }

        .dapodik-bottom-center {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: 103px;
        }
        .dapodik-bottom-center img { width: 100%; }

        /* Back Card */
        .back-top {
            text-align: center;
            padding-top: 17px;
            line-height: 1.1;
        }
        .back-top img { height: 46px; margin-bottom: 3px; }
        .back-top .text-small { font-size: 7px; }
        
        .back-bar {
            background-color: #6a7f7c;
            color: #fff;
            text-align: center;
            padding: 7px 0;
            margin-top: 17px;
            line-height: 1.2;
        }
        .back-bar-main { font-size: 15px; font-weight: bold; margin: 0; letter-spacing: 1px; }
        .back-bar-sub { font-size: 8px; margin: 0; }

        .back-links {
            text-align: center;
            font-size: 10px;
            color: #444;
            margin-top: 17px;
            line-height: 1.4;
        }
        
        .back-dapodik {
            text-align: center;
            margin-top: 20px;
        }
        .back-dapodik img {
            width: 103px;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/Tutwuri.png');
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
        $logoSrc = 'data:image/png;base64,' . $logoData;

        $qrUrl = 'https://quickchart.io/qr?text=' . urlencode($student->nisn) . '&size=100&margin=0';
        $qrContext = stream_context_create([
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
            'http' => ['ignore_errors' => true, 'timeout' => 5]
        ]);
        $qrContent = @file_get_contents($qrUrl, false, $qrContext);
        $qrSrc = $qrContent ? 'data:image/png;base64,' . base64_encode($qrContent) : '';

        $dapodikPath = base_path('raw/dapodik.png');
        $dapodikData = file_exists($dapodikPath) ? base64_encode(file_get_contents($dapodikPath)) : '';
        $dapodikSrc = 'data:image/png;base64,' . $dapodikData;

        $nisnLogPath = base_path('raw/nisn.png');
        $nisnLogData = file_exists($nisnLogPath) ? base64_encode(file_get_contents($nisnLogPath)) : '';
        $nisnLogSrc = 'data:image/png;base64,' . $nisnLogData;

        $userPath = base_path('raw/user.png');
        $userData = file_exists($userPath) ? base64_encode(file_get_contents($userPath)) : '';
        $userSrc = 'data:image/png;base64,' . $userData;
    @endphp

    <div class="container">
        <!-- FRONT CARD -->
        <div class="card">
            <div class="watermark">
                <img src="{{ $logoSrc }}">
            </div>

            <!-- Header -->
            <div class="front-header">
                <div class="logo-kemdikbud">
                    <img src="{{ $logoSrc }}" class="logo-img">
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
                <div class="left-section">
                    <img src="{{ $nisnLogSrc }}" class="nisn-logo">
                    <img src="{{ $userSrc }}" class="user-photo">
                </div>

                <div class="student-data">
                    <table>
                        <tr>
                            <td class="col-label">NISN</td>
                            <td>: <b>{{ $student->nisn }}</b></td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>: {{ $student->name }}</td>
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

                <div class="clear"></div>
                
                <div class="qr-bottom-right">
                    <img src="{{ $qrSrc }}">
                </div>
                
                <div class="dapodik-bottom-center">
                    <img src="{{ $dapodikSrc }}">
                </div>
            </div>
        </div>

        <!-- BACK CARD -->
        <div class="card">
            <div class="watermark">
                <img src="{{ $logoSrc }}">
            </div>

            <div class="back-top" style="position: relative; z-index:10;">
                <img src="{{ $logoSrc }}"><br>
                <span class="text-blue" style="font-size:13px;">Kemen</span><span class="text-orange" style="font-size:13px;">dikdasmen</span><br>
                <span class="text-small" style="font-size:7px;">Pusat Data dan Teknologi Informasi</span>
            </div>

            <div class="back-bar" style="position: relative; z-index:10;">
                <p class="back-bar-main">KARTU NISN</p>
                <p class="back-bar-sub">NOMOR INDUK SISWA NASIONAL</p>
            </div>

            <div class="back-links" style="position: relative; z-index:10;">
                Link Resmi NISN :<br>
                https://nisn.data.kemdikdasmen.go.id
              </div>

            <div class="back-dapodik" style="position: relative; z-index:10;">
                <img src="{{ $dapodikSrc }}">
            </div>
        </div>
    </div>

</body>
</html>
