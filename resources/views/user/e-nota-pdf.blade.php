<!DOCTYPE html>
<html>
<head>
    <title>E-NOTA</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    </style>
</head>
<body>

<div class="text-center mb-4">
    <div class="d-flex align-items-center justify-content-center">
        <img src="{{ public_path('assets/img/logo-komdigi.png') }}" alt="Kop KOMDIGI" class="me-3" style="height: 100px;">
        <div class="text-start">
            <h5 class="mb-0 fw-bold">KEMENTERIAN KOMUNIKASI DAN DIGITAL RI</h5>
            <h6 class="mb-0">SEKRETARIAT JENDERAL</h6>
            <h6 class="mb-1">BIRO SUMBER DAYA MANUSIA DAN ORGANISASI</h6>
            <small>Jl. Medan Merdeka Barat No. 9, Jakarta 10110 Telp. (021) 3865189 www.komdigi.go.id</small>
        </div>
    </div>
    <hr class="my-3">
    <h5 class="fw-bold text-decoration-underline">MEMO - DINAS</h5>
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
