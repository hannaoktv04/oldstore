<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stock Opname - {{ $session->id }}</title>
    <link href="{{ public_path('assets/css/pdfSO.css') }}" rel="stylesheet">
</head>

<body>

    <div class="footer">
        <div class="page-number"></div>
    </div>

    <header>
        <div class="header-container">
            <div class="header-flex">
                <img src="{{ public_path('assets/img/logo-komdigi.png') }}" alt="Kop KOMDIGI" class="header-logo">
                <div class="header-text">
                    <h4 class="fw-bold">KEMENTERIAN KOMUNIKASI DAN DIGITAL RI</h4>
                    <h5 class="fw-bold">INSPEKTORAT JENDERAL</h5>
                    <h5 class="fw-bold">BIRO SUMBER DAYA MANUSIA DAN ORGANISASI</h5>
                    <p>Jl. Medan Merdeka Barat No. 9, Jakarta 10110 Telp. (021) 3865189 www.komdigi.go.id</p>
                </div>
            </div>
            <div class="clear"></div>
            <hr class="solid-black">
        </div>
    </header>

    <main>
        <h5 class="report-title">LAPORAN STOCK OPNAME</h5>
        <div class="meta-info">
            <div class="date-opname">
                <span>Tanggal Opname: {{ \Carbon\Carbon::parse($session->tanggal_mulai)->format('d M Y') }}</span>
            </div>
            <div>
                <span>Nomor: {{ str_pad($session->id, 3, '0', STR_PAD_LEFT) }}/NNNN/{{ now()->year }}</span><br>
                <span>Periode: {{ $session->periode_bulan }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Stok Sistem</th>
                    <th>Stok Fisik</th>
                    <th>Selisih</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $index => $item)
                @php
                $opnameData = $item->stockOpnames->first();
                $qtySistem = $item->stocks ? $item->stocks->qty : 0;
                $qtyFisik = $opnameData ? $opnameData->qty_fisik : null;
                $selisih = $opnameData ? $opnameData->selisih : null;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="text-center">{{ $item->satuan }}</td>
                    <td class="text-center">{{ number_format($qtySistem) }}</td>
                    <td class="text-center">
                        @if (!is_null($qtyFisik))
                        {{ number_format($qtyFisik) }}
                        @endif
                    </td>
                    <td class="text-center {{ $selisih < 0 ? 'text-danger' : '' }}">
                        @if (!is_null($selisih))
                        {{ number_format($selisih) }}
                        @endif
                    </td>
                    <td>{{ $opnameData->catatan ?? '' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data barang untuk sesi opname ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signature-section">
            <div class="signature-box">
                <p>Mengetahui,</p>
                <div class="signature-space"></div>
                <p>(Nama Pejabat)</p>
            </div>
            <div class="signature-box right">
                <p>Pelaksana Stock Opname,</p>
                <div class="signature-space"></div>
                <p>{{ auth()->user()->nama ?? 'Nama Pelaksana' }}</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="printed-at">
            Dicetak pada: {{ now()->format('d M Y H:i') }}
        </div>
    </main>

</body>

</html>
