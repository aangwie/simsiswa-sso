<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mencetak Kartu NISN - {{ $student->name }}</title>
    <!-- Library untuk merubah HTML ke Canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- Library untuk merubah Canvas ke PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        /* UI untuk loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: #ffffff;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #4f46e5;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Area yang akan di-capture (kita sembunyikan dibalik loading tapi tetap ada di DOM agar bisa digambar) */
        .capture-container {
            width: 210mm; /* Lebar kertas A4 */
            padding: 20mm;
            background: white;
            box-sizing: border-box;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1; /* Dibawah loading overlay */
        }

        .card {
            width: 8.6cm;
            height: 5.4cm;
            display: inline-block;
            margin-right: 0px;
            margin-bottom: 20px;
            border: 1px solid #999;
            border-radius: 6px;
            overflow: hidden;
            background: #e0f7fa; /* Fallback */
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
            width: 150px;
        }

        /* Front Card */
        .front-header {
            width: 100%;
            padding: 5px 7px;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
        }
        .logo-kemdikbud {
            float: left;
            width: 129px;
        }
        .logo-img {
            float: left;
            height: 22px;
            margin-right: 4px;
        }
        .logo-text {
            float: left;
            margin-top: 2px;
            line-height: 1;
        }
        .text-blue { color: #00a8e8; font-weight: bold; font-size: 10px; }
        .text-orange { color: #f39c12; font-weight: bold; font-size: 10px; }
        .text-small { font-size: 5px; color: #555; display: block; margin-top: 1px; }

        .title-box {
            float: right;
            text-align: center;
            line-height: 1.1;
        }
        .title-main { color: #2980b9; font-size: 11px; font-weight: bold; margin: 0; }
        .title-sub { color: #444; font-size: 6px; margin: 0; }
        
        .clear { clear: both; }

        .front-content {
            width: 100%;
            padding: 5px 8px;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
        }

        .left-section {
            float: left;
            width: 64px;
            text-align: center;
        }
        .nisn-logo {
            width: 39px;
            margin-bottom: 2px;
        }
        .user-photo {
            width: 64px;
            height: 85px;
            border: 1px solid #ccc;
            background: #fff;
            padding: 2px;
            box-sizing: border-box;
            object-fit: cover;
            opacity: 0.5;
        }

        .student-data {
            float: left;
            width: 185px;
            margin-left: 13px;
            margin-top: 22px;
        }
        .student-data table {
            width: 100%;
            font-size: 8.5px;
            border-collapse: collapse;
            color: #111;
        }
        .student-data td { padding: 2px 0; vertical-align: top; }
        .col-label { width: 64px; }

        .qr-bottom-right {
            position: absolute;
            bottom: 9px;
            right: 9px;
            width: 42px;
            height: 42px;
            background: #fff;
            padding: 1px;
            box-sizing: border-box;
            border: 1px solid #ccc;
        }
        .qr-bottom-right img { width: 100%; height: 100%; }

        .dapodik-bottom-center {
            position: absolute;
            bottom: 9px;
            left: 50%;
            transform: translateX(-50%);
            width: 77px;
        }
        .dapodik-bottom-center img { width: 100%; }

        /* Back Card */
        .back-top {
            text-align: center;
            padding-top: 13px;
            line-height: 1.1;
        }
        .back-top img { height: 36px; margin-bottom: 2px; }
        .back-top .text-small { font-size: 6px; }
        
        .back-bar {
            background-color: #6a7f7c;
            color: #fff;
            text-align: center;
            padding: 5px 0;
            margin-top: 13px;
            line-height: 1.2;
        }
        .back-bar-main { font-size: 11px; font-weight: bold; margin: 0; letter-spacing: 1px; }
        .back-bar-sub { font-size: 6px; margin: 0; }

        .back-links {
            text-align: center;
            font-size: 8px;
            color: #444;
            margin-top: 13px;
            line-height: 1.4;
        }
        
        .back-dapodik {
            text-align: center;
            margin-top: 15px;
        }
        .back-dapodik img {
            width: 77px;
        }
    </style>
</head>
<body>

    <!-- Loading UI -->
    <div class="loading-overlay" id="loading">
        <div class="spinner"></div>
        <h2 style="color: #1f2937;">Sedang Memproses PDF...</h2>
        <p style="color: #6b7280; margin-top: 5px;">Mohon tunggu sebentar, file PDF akan terdownload secara otomatis.</p>
    </div>

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

    <!-- Elemen yang akan dicapture menjadi gambar/PDF -->
    <div class="capture-container" id="capture-area">
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

        <br>

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

    <!-- Script Proses Konversi HTML ke PDF di Sisi Klien -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Beri jeda 1.5 detik untuk memastikan semua asset (font, dll) termuat sempurna oleh browser
            setTimeout(async function() {
                try {
                    const { jsPDF } = window.jspdf;
                    
                    const element = document.getElementById('capture-area');
                    
                    // Render elemen menjadi gambar canvas kualitas tinggi
                    const canvas = await html2canvas(element, {
                        scale: 3, // Kualitas HD
                        useCORS: true,
                        backgroundColor: '#ffffff'
                    });
                    
                    const imgData = canvas.toDataURL('image/jpeg', 1.0);
                    
                    // Buat instance PDF baru ukuran A4
                    const pdf = new jsPDF({
                        orientation: 'portrait',
                        unit: 'mm',
                        format: 'a4'
                    });
                    
                    // Ukuran A4 adalah 210 x 297 mm
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = pdf.internal.pageSize.getHeight();
                    
                    // Hitung tinggi berdasarkan aspek rasio
                    const imgProps = pdf.getImageProperties(imgData);
                    const printHeight = (imgProps.height * pdfWidth) / imgProps.width;
                    
                    // Masukkan gambar ke PDF
                    pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, printHeight);
                    
                    // Otomatis unduh PDF
                    pdf.save('Kartu_NISN_{{ $student->nisn }}_{{ $student->name }}.pdf');
                    
                    // Ubah UI loading menjadi Sukses
                    document.getElementById('loading').innerHTML = `
                        <div style="text-align: center; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                            <svg style="width: 80px; height: 80px; margin: 0 auto 15px auto; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h2 style="color: #1f2937; margin-bottom: 10px;">PDF Berhasil Diunduh!</h2>
                            <p style="color: #6b7280; margin-bottom: 25px;">Silakan periksa folder Download pada komputer Anda.</p>
                            <button onclick="window.close()" style="padding: 12px 24px; background: #4f46e5; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold; transition: background 0.3s;">
                                Tutup Halaman Ini
                            </button>
                        </div>
                    `;
                } catch (error) {
                    console.error("Error generating PDF", error);
                    document.getElementById('loading').innerHTML = `
                        <div style="text-align: center; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                            <svg style="width: 80px; height: 80px; margin: 0 auto 15px auto; color: #ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h2 style="color: #1f2937; margin-bottom: 10px;">Gagal Membuat PDF</h2>
                            <p style="color: #6b7280; margin-bottom: 25px;">Terjadi kesalahan pada browser Anda. Pastikan browser mendukung Canvas.</p>
                            <button onclick="window.close()" style="padding: 12px 24px; background: #ef4444; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold;">
                                Tutup
                            </button>
                        </div>
                    `;
                }
            }, 1500); // Tunggu 1.5 detik
        });
    </script>
</body>
</html>
