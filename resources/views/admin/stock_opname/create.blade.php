@extends('layouts.admin')

@section('title', 'Mulai Sesi Stock Opname')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Mulai Sesi Stock Opname Baru</h4>
    </div>

    <form action="{{ route('admin.stock_opname.store') }}" method="POST">
        @csrf
        <div class="row mb-4 border-bottom pb-4">
            <div class="col-md-4 mb-3">
                <label class="form-label small text-muted">Periode Bulan</label>
                <input type="text" name="periode_bulan" class="form-control form-control-sm" required>
            </div>

            <div class="col-md-4 mb-3">
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
            <button type="submit" class="btn btn-success">Mulai Sesi</button>
            <a href="{{ route('admin.stock_opname.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#datatable-opname').DataTable({
            paging: true,
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
                infoEmpty: "Tidak ada data tersedia",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });
    });
</script>
@endpush
