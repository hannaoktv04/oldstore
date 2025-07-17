<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $purchaseOrder->nomor_po }}</title>
    <link href="{{ public_path('assets/css/pdfPO.css') }}" rel="stylesheet">
</head>

<body>
    <div class="kop-surat">
        <img src="{{ public_path('assets/img/logo-komdigi.png') }}" class="logo">
        <div class="header-text">
            <h4 style="margin:0;padding:0;">KEMENTERIAN KOMUNIKASI DAN DIGITAL RI</h4>
            <h5 style="margin:1px 0;padding:0;">SEKRETARIAT JENDERAL</h5>
            <h5 style="margin:1px 0;padding:0;">BIRO SUMBER DAYA MANUSIA DAN ORGANISASI</h5>
            <small>Jl. Medan Merdeka Barat No. 9, Jakarta 10110 Telp. (021) 3865189 www.komdigi.go.id</small>
        </div>
    </div>

    <div class="judul-dokumen">DAFTAR PENGAJUAN PEMBELIAN</div>
    <div style="text-align: right">
        <span>Jakarta, {{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d M Y') }}</span>
    </div>
    <div style="text-align: left">
        <span>Nomor: {{ $purchaseOrder->nomor_po }}</span>
    </div>
    <div class="mb-3">
        <p>Kepada Yth. <br> Vendor/Penyedia Barang</p>
        <p>
            Dengan hormat, <br>
            Sehubungan dengan kebutuhan persediaan barang pada unit Staf Rumah Tangga, bersama ini kami
            mengajukan pemesanan barang sebagaimana terlampir di bawah ini :
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:15%;">Kode Barang</th>
                <th>Nama Barang</th>
                <th style="width:10%;">Satuan</th>
                <th style="width:15%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseOrder->details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->item->kode_barang ?? '-' }}</td>
                    <td>{{ $detail->item->nama_barang ?? '-' }}</td>
                    <td class="text-center">{{ $detail->item->satuan ?? '-' }}</td>
                    <td class="text-right">{{ number_format($detail->qty) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">TOTAL</th>
                <th class="text-right">{{ number_format($purchaseOrder->details->sum('qty')) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4">
        <div class="mb-3">
            <label class="fw-bold">Status:</label>
            <span
                class="badge
                @switch($purchaseOrder->status)
                    @case('draft') bg-secondary @break
                    @case('submitted') bg-primary @break
                    @case('received') bg-success @break
                    @default bg-dark
                @endswitch">
                {{ ucfirst($purchaseOrder->status) }}
            </span>
        </div>
        <div class="mb-3">
            <label class="fw-bold">Catatan:</label>
            <p>{{ $purchaseOrder->catatan ?? '-' }}</p>
        </div>
    </div>

    <div class="ttd-area">
        <div class="ttd-box">
            <p>Menyetujui,</p>
            <br><br><br>
            <p>_________________________</p>
            <p>(Nama & Tanda Tangan)</p>
        </div>
        <div class="ttd-box">
            <p>Pemohon,</p>
            {{-- <p>{{ Auth::user()->jabatan ?? 'Admin Purchasing' }}</p> --}}
            <br><br><br>
            <p>_________________________</p>
            <p>{{ Auth::user()->nama }}</p>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}
    </div>
</body>

</html>
