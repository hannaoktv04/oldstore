<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-NOTA</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 30px;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .header img {
            height: 100px;
            margin-right: 15px;
        }
        .header-info h3, .header-info h4 {
            margin: 0;
        }
        .header-info small {
            font-size: 10px;
        }
        hr {
            margin: 10px 0 20px;
            border: 1px solid #000;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
        }
        table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            text-align: center;
        }
        .footer {
            margin-top: 40px;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="text-center">
    <div class="header">
        <img src="{{ public_path('assets/img/logo-komdigi.png') }}" alt="Logo KOMDIGI">
        <div class="header-info text-start">
            <h3 style="font-weight: bold;">KEMENTERIAN KOMUNIKASI DAN DIGITAL RI</h3>
            <h4>SEKRETARIAT JENDERAL</h4>
            <h4>BIRO SUMBER DAYA MANUSIA DAN ORGANISASI</h4>
            <small>Jl. Medan Merdeka Barat No. 9, Jakarta 10110 Telp. (021) 3865189 www.komdigi.go.id</small>
        </div>
    </div>
    <hr>
    <h3 style="text-decoration: underline; font-weight: bold;">MEMO - DINAS</h3>
</div>

<div class="text-end">
    <p>Jakarta, {{ now()->format('d M Y') }}</p>
</div>
<div class="text-start mb-2">
    <p>Nomor: {{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}/NNNN/{{ now()->year }}</p>
</div>

<p>Kepada Yth. <br> Kepala Bagian Keuangan dan Rumah Tangga</p>
<p>Berikut ini adalah permintaan pengajuan persediaan barang sebagai berikut:</p>

<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th>Nama Barang</th>
            <th style="width: 15%;">Jumlah</th>
            <th style="width: 20%;">Diberikan</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($request->details as $index => $detail)
        <tr>
            <td style="text-align: center;">{{ $index + 1 }}</td>
            <td>{{ $detail->item->nama_barang }}</td>
            <td style="text-align: center;">{{ $detail->qty_requested }} {{ $detail->item->satuan }}</td>
            <td style="text-align: center;">{{ $detail->qty_approved ?? '-' }}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="signature-table">
    <tr>
        <td>
            Mengetahui,<br>
            Kasubbag Rumah Tangga<br><br><br><br>
            __________________________
        </td>
        <td>
            Yang Meminta,<br>
            {{ $request->user->jabatan }}<br><br><br><br>
            {{ $request->user->nama }}
        </td>
    </tr>
</table>

<div class="footer">
    Dicetak pada: {{ now()->format('d M Y H:i') }}
</div>

</body>
</html>
