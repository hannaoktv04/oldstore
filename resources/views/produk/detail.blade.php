@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-6 mb-3">
            <img src="{{ asset($produk->image) }}" class="img-fluid rounded shadow-sm w-100" alt="{{ $produk->name }}">
        </div>
        <div class="col-lg-6">
            <h6 class="text-muted">{{ $produk->kategori }}</h6>
            <h2 class="fw-bold text-break">{{ $produk->name }}</h2>
            <p class="text-break">{{ $produk->deskripsi }}</p>
            <p><strong>{{ $produk->stock }}</strong> produk tersedia</p>
            <p>Satuan: <strong>{{ $produk->satuan }}</strong></p>

            <form method="POST" action="#">
                @csrf
                <label for="jumlah" class="form-label">Jumlah</label>
                <input type="number" name="jumlah" value="1" min="1" max="{{ $produk->stock }}" class="form-control mb-3" />

                <button type="submit" class="btn btn-success w-100">Tambahkan ke Keranjang</button>
            </form>
        </div>
    </div>
</div>
@endsection