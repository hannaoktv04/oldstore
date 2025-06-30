@extends('layouts.admin')

@section('title', 'Input Stock Opname')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Input Stock Opname - Periode {{ $session->periode_bulan }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.stock_opname.update', $session->id) }}" method="POST">


            @csrf
            @method('PUT')

            <table class="table table-bordered table-striped" id="item-table">
                <thead class="table-light">
                    <tr>
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
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>{{ number_format($item->stocks->qty ?? 0, 2) }}</td>
                            <td>
                                <input type="hidden" name="item_id[]" value="{{ $item->id }}">
                                <input type="number" name="qty_fisik[]" step="0.01" min="0" class="form-control qty-fisik" data-sistem="{{ $item->stocks->qty ?? 0 }}">
                            </td>
                            <td class="selisih">-</td>
                            <td><input type="text" name="catatan[]" class="form-control" placeholder="Opsional..."></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">Simpan Opname</button>
                <a href="{{ route('admin.stock_opname.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection

