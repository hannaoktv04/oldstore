@extends('peri::layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Buat Daftar Pengajuan</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.purchase_orders.store') }}" method="POST">
            @csrf
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="fw-semibold">Nomor PO</label>
                    <input type="text" name="nomor_po" class="form-control bg-light text-muted" value="{{ $nomor_po }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="fw-semibold">Tanggal PO</label>
                    <input type="date" name="tanggal_po" class="form-control" value="{{ old('tanggal_po') }}" required>
                </div>
            </div>

            <hr>
            <h5 class="mt-3">Tambah Item</h5>
            <div class="row align-items-end mb-3">
                <div class="col-md-4">
                    <label class="fw-semibold">Item</label>
                    <select class="form-control" id="item-select">
                        <option value="">Pilih Item</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" data-kode="{{ $item->kode_barang }}"
                                data-nama="{{ $item->nama_barang }}" data-satuan="{{ $item->satuan->nama_satuan ?? '-' }}" data-stok="{{ $item->stocks->qty ?? 0}}">
                                {{ $item->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="fw-semibold">Satuan</label>
                    <input type="text" class="form-control" id="unit" readonly>
                </div>
                <div class="col-md-2">
                    <label class="fw-semibold">stok akhir</label>
                    <input type="number" class="form-control" id="stok-akhir" readonly>
                </div>
                <div class="col-md-2">
                    <label class="fw-semibold">Qty</label>
                    <input type="number" class="form-control" id="qty" step="0.01" min="0.01">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-lg btn-outline-primary w-full" id="add-item">
                        <span class="bi bi-plus"></span> Tambah Item
                    </button>
                </div>
            </div>

            <table class="table table-bordered" id="po-table">
                <thead>
                    <tr>
                        <th class="text-center">Kode Item</th>
                        <th class="text-center">Nama Item</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body"></tbody>
            </table>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.purchase_orders.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/peri/crud-po.js') }}"></script>
@endpush


