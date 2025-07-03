@extends('layouts.admin')

@section('content')
<style>
    @media print {
        body * {
            visibility: hidden !important;
        }

        .resi-wrapper, .resi-wrapper * {
            visibility: visible !important;
        }

        .resi-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background: white;
            padding: 20px;
        }

        nav, aside, .sidebar, .topbar, .footer, .btn {
            display: none !important;
        }
    }

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

    .resi-section {
        margin-bottom: 1rem;
    }

    .resi-section p {
        margin: 2px 0;
    }

    .table-barang {
        width: 100%;
        border-collapse: collapse;
    }

    .table-barang th,
    .table-barang td {
        border: 1px solid #000;
        padding: 4px 6px;
        text-align: left;
    }

    .qr-img {
        background: #fff;
        padding: 20px;
        display: inline-block;
        margin-top: 20px;
    }
</style>
@php
    $kodeResi = 'KP' . str_pad($pengajuan->id, 6, '0', STR_PAD_LEFT);
    $qrLink = route('staff-pengiriman.konfirmasi', $kodeResi);
@endphp
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

    <div class="resi-section">
        <p><strong>Pemohon:</strong> {{ $pengajuan->user->nama }}</p>
        <p><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($pengajuan->tanggal_permintaan)->format('d F Y') }}</p>
        <p><strong>Tanggal Pengiriman:</strong> {{ \Carbon\Carbon::parse($pengajuan->tanggal_pengiriman)->format('d F Y, H:i') }}</p>
    </div>

    <div class="resi-section">
        <h6>Daftar Barang</h6>
        <table class="table-barang">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                </tr>
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
    </div>

    <div class="resi-section text-center">
        <div class="qr-img">
            <p style="font-size: 13px;">Scan QR untuk Konfirmasi Kurir</p>
            <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code">
            <div style="font-size: 12px; margin-top: 8px;">{{ $kodeResi }}</div>
        </div>
    </div>

    <div class="text-center mt-4">
        <button onclick="window.print()" class="btn btn-primary">Cetak Resi</button>
    </div>
</div>
@endsection
