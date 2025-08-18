@extends('peri::layouts.admin')

@section('title', 'Detail Stock Opname')

@section('content')
<div class="mb-4">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.stock_opname.index') }}">Stock Opname</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $session->periode_bulan }}</li>
        </ol>
    </nav>
</div>
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="container-fluid" id="print_out">
            <div class="text-center mb-4">
                <div class="d-flex align-items-center justify-content-space-between">
                    <img src="{{ asset('assets/img/logo-komdigi.png') }}" alt="Kop KOMDIGI" class="me-3"
                        style="height: 80px;">
                    <div class="text-start px-10">
                        <h4 class="mb-0 fw-bold">KEMENTERIAN KOMUNIKASI DAN DIGITAL RI</h4>
                        <h5 class="mb-0">INSPEKTORAT JENDERAL</h5>
                        <h5 class="mb-0">BIRO SUMBER DAYA MANUSIA DAN ORGANISASI</h5>
                        <p>Jl. Medan Merdeka Barat No. 9, Jakarta 10110 Telp. (021) 3865189
                            www.komdigi.go.id</p>
                    </div>
                </div>
                <hr style="border: 1px solid black;">
                <h5 class="fw-bold text-decoration-underline">LAPORAN STOCK OPNAME</h5>
                <div class="text-end">
                    <span>Tanggal Opname: {{ \Carbon\Carbon::parse($session->tanggal_mulai)->format('d M Y') }}</span>
                </div>
                <div class="text-start">
                    <span>Nomor: {{ str_pad($session->id, 3, '0', STR_PAD_LEFT) }}/NNNN/{{ now()->year }}</span>
                </div>
                <div class="text-start">
                    <span>Periode: {{ $session->periode_bulan }}</span><br>
                </div>
            </div>

            <table class="table table-bordered">
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
                    @foreach ($items as $index => $item)
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
                        <td class="text-center">{{ $item->satuan->nama_satuan }}</td>
                        <td class="text-center">{{ number_format($qtySistem) }}</td>
                        <td class="text-center">
                            @if (!is_null($qtyFisik))
                            {{ number_format($qtyFisik) }}
                            @else
                            <span class="text-muted"></span>
                            @endif
                        </td>
                        <td class="{{ $selisih < 0 ? 'text-danger' : '' }} text-center">
                            @if (!is_null($selisih))
                            {{ number_format($selisih) }}
                            @else
                            <span class="text-muted"></span>
                            @endif
                        </td>
                        <td>{{ $opnameData->catatan ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold">Status:</label>
                        <span class="badge
                            @switch($session->status)
                                @case('menunggu') bg-secondary @break
                                @case('aktif') bg-primary @break
                                @case('selesai') bg-success @break
                                @default bg-dark
                            @endswitch">
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Catatan:</label>
                        <p>{{ $session->catatan ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col text-center">
                    <p>Mengetahui,</p>
                    <br><br><br>
                    <p>_________________________</p>
                    <p>(Nama & Tanda Tangan)</p>
                </div>
                <div class="col text-center">
                    <p>Pelaksana Stock Opname,</p>
                    <br><br><br>
                    <p>_________________________</p>
                    <p>{{ auth()->user()->nama }}</p>
                </div>
            </div>

        </div>

        <div class="text-start mt-4 mb-2">
            <a href="{{ route('admin.stock_opname.downloadPdf', $session->id) }}" class="btn btn-flat btn-success"
                target="_blank">
                <i class="fas fa-file-pdf"></i> Print
            </a>
            <a href="{{ route('admin.stock_opname.index') }}" class="btn btn-flat btn-dark">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
            $('#print').click(function() {
                var printContents = document.getElementById('print_out').innerHTML;
                var originalContents = document.body.innerHTML;

                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                window.location.reload();
            });
        });
</script>
@endsection
