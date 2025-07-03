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
            gap: 15px;
            margin-bottom: 10px;
        }
        .header img {
            height: 100px;
        }
        .header-info h5, .header-info h6 {
            margin: 0;
        }
        .text-center {
            text-align: center;
        }
        .text-end {
            text-align: right;
        }
        .text-start {
            text-align: left;
        }
        hr {
            margin: 10px 0 20px;
            border: 1px solid #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        table th {
            background-color: #f2f2f2;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            text-align: center;
        }
        .signature-table td {
            border: none;
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
        <img src="{{ public_path('assets/img/logo-komdigi.png') }}" alt="Kop KOMDIGI">
        <div class="header-info text-start">
            <h5 style="font-weight: bold;">KEMENTERIAN KOMUNIKASI DAN DIGITAL RI</h5>
            <h6>SEKRETARIAT JENDERAL</h6>
            <h6>BIRO SUMBER DAYA MANUSIA DAN ORGANISASI</h6>
            <small>Jl. Medan Merdeka Barat No. 9, Jakarta 10110 Telp. (021) 3865189 www.komdigi.go.id</small>
        </div>
    </div>
    <hr>
    <h5 style="text-decoration: underline; font-weight: bold;">MEMO - DINAS</h5>
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
            <th>No</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Diberikan</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($request->details as $index => $detail)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td style="text-align: left;">{{ $detail->item->nama_barang }}</td>
            <td>{{ $detail->qty_requested }} {{ $detail->item->satuan }}</td>
            <td>{{ $detail->qty_approved ?? '-' }}</td>
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
