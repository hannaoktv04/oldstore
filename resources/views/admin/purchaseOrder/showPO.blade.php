@extends('layouts.admin')

@section('title', 'Detail Purchase Order')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="container-fluid" id="print_out">
            <div class="text-center mb-4">
                <div class="d-flex align-items-center justify-content-center">
                    <img src="{{ asset('assets/img/logo-komdigi.png') }}" alt="Kop KOMDIGI" class="me-3"
                        style="height: 100px;">
                    <div class="text-center">
                        <h5 class="mb-0 fw-bold">KEMENTERIAN KOMUNIKASI DAN DIGITAL RI</h5>
                        <h6 class="mb-0">SEKRETARIAT JENDERAL</h6>
                        <h6 class="mb-1">BIRO SUMBER DAYA MANUSIA DAN ORGANISASI</h6>
                        <small>Jl. Medan Merdeka Barat No. 9, Jakarta 10110 Telp. (021) 3865189
                            www.komdigi.go.id</small>
                    </div>
                </div>
                <hr class="my-3">
                <h5 class="fw-bold text-decoration-underline">DAFTAR PENGAJUAN PEMBELIAN</h5>
                <div class="text-start">
                    <span>Jakarta, {{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d M Y') }}</span><br>
                    <span>Nomor: {{ $purchaseOrder->nomor_po }}</span>
                </div>
            </div>

            <div class="mb-4">
                <p>Kepada Yth. <br> Vendor/Penyedia Barang</p>
                <p>
                    Dengan hormat, <br>
                    Sehubungan dengan kebutuhan persediaan barang pada unit Staf Rumah Tangga, bersama ini kami
                    mengajukan pemesanan barang sebagaimana terlampir di bawah ini :
                </p>
            </div>

            <table class="table table-bordered">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Kode Barang</th>
                        <th>Nama Barang</th>
                        <th style="width: 10%;">Satuan</th>
                        <th style="width: 15%;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalQty = 0; @endphp
                    @foreach ($purchaseOrder->details as $index => $detail)
                    @php $totalQty += $detail->qty; @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $detail->item->kode_barang ?? '-' }}</td>
                        <td>{{ $detail->item->nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ $detail->item->satuan ?? '-' }}</td>
                        <td class="text-center">{{ number_format($detail->qty) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Total</th>
                        <th class="text-center">{{ number_format($totalQty) }}</th>
                    </tr>
                </tfoot>
            </table>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold">Status:</label>
                        <span class="badge
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
            </div>

            <div class="row mt-5">
                <div class="col text-center">
                    <p>Menyetujui,</p>
                    <br><br><br>
                    <p>_________________________</p>
                    <p>(Nama & Tanda Tangan)</p>
                </div>
                <div class="col text-center">
                    <p>Pemohon,</p>
                    <p>{{ Auth::user()->jabatan ?? 'Admin' }}</p>
                    <br><br><br>
                    <p>{{ Auth::user()->nama }}</p>
                </div>
            </div>

            <div class="text-start mt-5">
                <em>Dicetak pada: {{ now()->format('d M Y H:i') }}</em>
            </div>
        </div>

        <div class="text-start mt-4 mb-2">
            <a href="{{ route('admin.purchase_orders.downloadPdf', $purchaseOrder->id) }}"
   class="btn btn-flat btn-success" target="_blank">
   <i class="fas fa-file-pdf"></i> Print
</a>
            <a href="{{ route('admin.purchase_orders.index') }}" class="btn btn-flat btn-dark">
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
