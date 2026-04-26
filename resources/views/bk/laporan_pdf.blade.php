<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan BK</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; margin: 20px; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 5px; }
        h2 { font-size: 14px; text-align: center; color: #555; margin-bottom: 20px; font-weight: normal; }
        .info { margin-bottom: 15px; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #4f46e5; color: white; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; }
        td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .badge-red { background-color: #fee2e2; color: #dc2626; }
        .footer { margin-top: 30px; font-size: 10px; color: #999; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .total-row { background-color: #eef2ff !important; font-weight: bold; }
        .header-line { border-bottom: 3px solid #4f46e5; margin-bottom: 20px; padding-bottom: 15px; }
    </style>
</head>
<body>
    <div class="header-line">
        <h1>LAPORAN BIMBINGAN KONSELING</h1>
        <h2>Rekap Pelanggaran Siswa</h2>
    </div>

    <div class="info">
        <strong>Filter:</strong> {{ $filterLabel }}<br>
        <strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}<br>
        <strong>Total Data:</strong> {{ $data->count() }} pelanggaran
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 70px;">Tanggal</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Pelanggaran</th>
                <th class="text-center" style="width: 50px;">Poin</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPoin = 0; @endphp
            @foreach($data as $index => $d)
            @php $totalPoin += $d->pelanggaran->poin ?? 0; @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $d->student->name ?? '-' }}</td>
                <td>{{ $d->student->schoolClass->name ?? '-' }}</td>
                <td>{{ $d->pelanggaran->nama_pelanggaran ?? '-' }}</td>
                <td class="text-center"><span class="badge badge-red">{{ $d->pelanggaran->poin ?? 0 }}</span></td>
                <td>{{ $d->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right">Total Poin:</td>
                <td class="text-center">{{ $totalPoin }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Laporan ini dicetak secara otomatis oleh sistem SIMSiswa &mdash; {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
