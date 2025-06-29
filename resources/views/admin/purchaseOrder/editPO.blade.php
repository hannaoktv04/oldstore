@extends('layouts.admin')

@section('title', 'Edit Purchase Order')

@section('content')
<div class="card px-1 py-3">
    <div class="card-header">
        <h4 class="card-title">Edit PO #{{ $purchaseOrder->nomor_po }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.purchase_orders.update', $purchaseOrder->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="fw-semibold">Kode PO</label>
                    <input type="text" name="nomor_po" class="form-control bg-light text-muted" value="{{ $purchaseOrder->nomor_po }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="fw-semibold">Tanggal PO</label>
                    <input type="date" name="tanggal_po" class="form-control"
                        value="{{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('Y-m-d') }}" required>
                </div>
            </div>

            <hr>
            <h5 class="mt-3">Tambah Item</h5>
            <div class="row align-items-end mb-3">
                <div class="col-md-5">
                    <label class="fw-semibold">Item</label>
                    <select class="form-control" id="item-select">
                        <option value="">Pilih Item</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" data-kode="{{ $item->kode_barang }}"
                                data-nama="{{ $item->nama_barang }}" data-satuan="{{ $item->satuan }}">
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
                    <label class="fw-semibold">Qty</label>
                    <input type="number" class="form-control" id="qty" step="0.01" min="0.01">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-outline-primary w-full" id="add-item"><i class="bi bi-plus"></i> Tambah Item</button>
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
                <tbody id="table-body">
                    @foreach ($purchaseOrder->details as $detail)
                        <tr>
                            <td>{{ $detail->item->kode_barang }}</td>
                            <td>
                                {{ $detail->item->nama_barang }}
                                <input type="hidden" name="item_id[]" value="{{ $detail->item->id }}">
                            </td>
                            <td>{{ $detail->item->satuan }}</td>
                            <td>
                                <input type="number" class="form-control" name="qty[]" value="{{ $detail->qty }}" step="0.01" min="0.01">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-item">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.purchase_orders.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
