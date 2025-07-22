@php
    $kodeResi = 'KP' . str_pad($pengajuan->id, 6, '0', STR_PAD_LEFT);
    $qrLink = route('staff-pengiriman.konfirmasi', $kodeResi);

    $result = \Endroid\QrCode\Builder\Builder::create()
        ->writer(new \Endroid\QrCode\Writer\PngWriter())
        ->data($qrLink)
        ->logoPath(public_path('assets/img/logo-komdigi.png'))
        ->logoResizeToWidth(100)
        ->size(200)
        ->margin(10)
        ->build();

    $qrBase64 = base64_encode($result->getString());
@endphp

<style>
    .resi-wrapper {
        max-width: 800px;
        margin: auto;
        border: 2px dashed #000;
        padding: 20px;
        background: #fff;
        font-size: 14px;
    }
    .resi-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .table-barang {
        width: 100%;
        border-collapse: collapse;
    }
    .table-barang th, .table-barang td {
        border: 1px solid #000;
        padding: 4px 6px;
    }
    .qr-img {
        background: #fff;
        padding: 20px;
        display: inline-block;
        margin-top: 20px;
    }
</style>

<div class="resi-wrapper">
    <div class="resi-header">
        <div>
            <h4 style="margin-bottom: 0;">Resi Pengajuan</h4>
            <strong>No. Resi:</strong> {{ $kodeResi }}
        </div>
        <div style="text-align: right;">
            <img src="{{ asset('assets/img/logo-komdigi.png') }}" height="40" alt="Logo">
        </div>
    </div>

    <p><strong>Pemohon:</strong> {{ $pengajuan->user->nama }}</p>
    <p><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($pengajuan->tanggal_permintaan)->format('d F Y') }}</p>
    <p><strong>Tanggal Pengiriman:</strong> {{ \Carbon\Carbon::parse($pengajuan->tanggal_pengiriman)->format('d F Y, H:i') }}</p>

    <h6>Daftar Barang</h6>
    <table class="table-barang">
        <thead>
            <tr><th>Nama Barang</th><th>Jumlah</th><th>Satuan</th></tr>
        </thead>
        <tbody>
            @foreach ($pengajuan->details as $detail)
            <tr>
                <td>{{ $detail->item->nama_barang }}</td>
                <td>{{ $detail->qty_requested }}</td>
                <td>{{ $detail->item->satuan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="qr-img" style="text-align:center; margin-top:20px;">
        <p style="font-size: 13px;">Scan QR untuk Konfirmasi Kurir</p>
        <img src="data:image/png;base64,{{ $qrBase64 }}" width="150">
        <div style="font-size: 12px; margin-top: 8px;">{{ $kodeResi }}</div>
    </div>
</div>

@push('scripts')
<script>
    function printResi(id) {
        const resiContent = document.getElementById('resi-' + id).innerHTML;
        const printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write('<html><head><title>Cetak Resi</title>');
        printWindow.document.write('<style>body{font-family: Arial; font-size: 12px;} table{width:100%; border-collapse:collapse;} th,td{border:1px solid #000; padding:5px;}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(resiContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
</script>
@endpush
