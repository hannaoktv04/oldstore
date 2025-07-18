@extends('layouts.admin')
@section('title', 'Input Stok Fisik')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Input Stok Fisik - {{ $purchaseOrder->periode_bulan }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.purchase-order.edit', $purchaseOrder->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row mb-4">
                <div class="col-md-6">
                    <label>Pilih Item</label>
                    <select name="item_id" class="form-select" required>
                        <option value="">-- Pilih Item --</option>
                        @foreach($availableItems as $item)
                            <option value="{{ $item->id }}">{{ $item->kode_barang }} - {{ $item->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Stok Fisik</label>
                    <input type="number" name="qty_fisik" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success">Tambah</button>
                </div>
            </div>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Stok Sistem</th>
                    <th>Stok Fisik</th>
                    <th>Selisih</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $item)
                <tr>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->pivot->qty_sistem }}</td>
                    <td>{{ $item->pivot->qty_fisik }}</td>
                    <td class="{{ $item->pivot->selisih < 0 ? 'text-danger' : 'text-success' }}">
                        {{ $item->pivot->selisih }}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning">Edit</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            <form action="{{ route('admin.stock_opname.submit', $purchaseOrder->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">Ajukan Approval</button>
            </form>
        </div>
    </div>
</div>
@endsection
