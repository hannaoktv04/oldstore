@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Koreksi Stok Barang</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.stok.koreksi.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="item_id" class="form-label">Pilih Barang</label>
            <select name="item_id" id="item_id" class="form-select" required>
                <option value="">-- Pilih --</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_barang }} (Stok: {{ $item->stocks->qty ?? 0 }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="qty_fisik" class="form-label">Stok Fisik Saat Ini</label>
            <input type="number" step="0.01" name="qty_fisik" id="qty_fisik" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="keterangan" class="form-label">Catatan / Keterangan</label>
            <textarea name="keterangan" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Simpan Koreksi</button>
    </form>
</div>
@endsection
