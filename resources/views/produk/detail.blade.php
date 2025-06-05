@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <img src="{{ asset($produk->image) }}" class="img-fluid" alt="{{ $produk->name }}">
        </div>
        <div class="col-md-6">
            <h4>{{ $produk->kategori }}</h4>
            <h2 class="fw-bold">{{ $produk->name }}</h2>
            <p>{{ $produk->deskripsi }}</p>
            <p><strong>{{ $produk->stock }}</strong> produk tersedia</p>

            <form method="POST" action="#">
                @csrf
                <label for="jumlah">Jumlah</label>
                <input type="number" name="jumlah" value="1" min="1" max="{{ $produk->stock }}" class="form-control mb-3" />

                <button type="submit" class="btn btn-success">Tambahkan ke Keranjang</button>
            </form>
        </div>
    </div>
</div>
@endsection