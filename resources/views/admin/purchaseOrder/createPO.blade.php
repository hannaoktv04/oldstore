@extends('layouts.admin')

@section('title', 'Create Purchase Order')

@section('content')
<div class="card px-1 py-3">
    <div class="card-header">
        <h4 class="card-title">Create New Purchase Order</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.purchase_orders.store') }}" method="POST">
            @csrf

            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="fw-semibold">Kode PO</label>
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
                    <button type="button" class="btn btn-sm btn-outline-success w-full" id="add-item">
                        <span class="bi bi-plus"></span> Tambah Item
                    </button>
                </div>
            </div>

            <table class="table table-bordered" id="po-table">
                <thead class="table-light">
                    <tr>
                        <th>Kode Item</th>
                        <th>Nama Item</th>
                        <th>Satuan</th>
                        <th>Qty</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body"></tbody>
            </table>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="{{ route('admin.purchase_orders.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

