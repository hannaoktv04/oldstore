<!DOCTYPE html>
<html>
<head>
    <title>E-NOTA</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        .kop { text-align: center; }
        .kop img { height: 80px; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="kop">
    <img src="{{ public_path('assets/img/logo-komdigi.png') }}" alt="Logo">
    <h4 style="margin: 0;">KEMENTERIAN KOMUNIKASI DAN DIGITAL RI</h4>
    <p style="margin: 0;">SEKRETARIAT JENDERAL - BIRO SUMBER DAYA MANUSIA DAN ORGANISASI</p>
    <small>Jl. Medan Merdeka Barat No. 9, Jakarta 10110 | www.komdigi.go.id</small>
    <hr>
    <h4>MEMO - DINAS</h4>
</div>

<p>Jakarta, {{ now()->format('d M Y') }}<br>
Nomor: {{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}/NNNN/{{ now()->year }}</p>

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
            <td>{{ $detail->item->nama_barang }}</td>
            <td>{{ $detail->qty_requested }} {{ $detail->item->satuan }}</td>
            <td>{{ $detail->qty_approved ?? '-' }}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>

<br><br>
<table style="border: none;">
    <tr style="border: none;">
        <td style="border: none; text-align: center;">
            Mengetahui,<br>
            Kasubbag Rumah Tangga<br><br><br><br>
            _________________________
        </td>
        <td style="border: none; text-align: center;">
            Yang Meminta,<br>
            {{ $request->user->jabatan }}<br><br><br><br>
            {{ $request->user->nama }}
        </td>
    </tr>
</table>

<p style="margin-top: 40px;"><em>Dicetak pada: {{ now()->format('d M Y H:i') }}</em></p>

</body>
</html>
