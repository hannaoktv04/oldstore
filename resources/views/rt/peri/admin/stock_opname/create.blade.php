@extends('peri::layouts.admin')

@section('title', 'Mulai Sesi Stock Opname')

@section('content')
<div class="card container-fluid py-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Mulai Sesi Stock Opname Baru</h4>
    </div>

    <form action="{{ route('admin.stock_opname.store') }}" method="POST">
        @csrf
        <div class="row mb-12 border-bottom pb-4">
            <div class="col-md-6 mb-3">
                <label class="form-label small text-muted">Periode Bulan</label>
                <input type="text" name="periode_bulan" class="form-control form-control-sm" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label small text-muted">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                    value="{{ now()->format('Y-m-d') }}"
                    min="{{ now()->subYears(1)->format('Y-m-d') }}"
                    max="{{ now()->addYear()->format('Y-m-d') }}"
                    required>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label small text-muted">Catatan Sesi</label>
                <textarea name="catatan" class="form-control form-control-sm" rows="2"
                    placeholder="Tambahkan catatan jika diperlukan"></textarea>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered" id="datatable-opname">
                <thead>
                    <tr>
                        <th class="text-center">Kode Barang</th>
                        <th class="text-center">Nama Barang</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Stok Sistem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td class="text-start">{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td class="text-center">{{ $item->satuan->nama_satuan ?? '-' }}</td>
                        <td class="text-end">{{ number_format($item->stocks->qty ?? 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>
            <a href="{{ route('admin.stock_opname.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#datatable-opname').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: false,
            columnDefs: [
                { orderable: false, targets: [3] }
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Tidak ada data tersedia"
            }
        });
    });
</script>
@endpush
