@extends('layouts.app')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">PERI</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Kategori Barang</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $produk->nama_barang }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-md-6">
            <div class="mb-3">
                <img src="{{ asset('assets/img/products/' . $produk->image) }}" class="img-fluid rounded-4 shadow-sm w-75" alt="{{ $produk->nama_barang }}">
            </div>
            <div class="d-flex gap-2 overflow-auto">
                @for($i = 1; $i <= 4; $i++)
                    <div class="flex-shrink-0">
                        <img src="{{ asset('assets/img/products/' . $produk->image) }}" class="img-thumbnail rounded-3" style="width: 70px; height: 70px; object-fit: cover;">
                    </div>
                @endfor
            </div>
        </div>

        <div class="col-md-6">
            <small class="text-muted">{{ $produk->kategori }}</small>
            <h3 class="fw-bold mt-1">{{ $produk->nama_barang }}</h3>
            <p class="text-muted mt-2">{{ $produk->deskripsi }}</p>

            {{-- Input Jumlah --}}
            <div class="mb-3">
                <label for="qty" class="form-label">Jumlah</label>
                <div class="input-group" style="width: 140px;">
                    <button type="button" class="btn btn-outline-secondary" onclick="ubahQty(-1)">-</button>
                    <input type="number" id="qty" value="1" min="1" max="{{ $produk->stok_minimum }}" class="form-control text-center">
                    <button type="button" class="btn btn-outline-secondary" onclick="ubahQty(1)">+</button>
                </div>
                <div id="qtyAlertContainer"></div>
            </div>

            <p class="text-muted">{{ $produk->stok_minimum }} produk tersedia</p>

            @if($produk->stok_minimum > 0)
                <form method="POST" action="{{ route('produk.pesanLangsung', ['id' => $produk->id]) }}" id="formPesan">
                    @csrf
                    <input type="hidden" name="qty" id="formPesanQty">
                    <button type="submit" class="btn btn-outline-secondary px-4 py-2">Pesan Langsung</button>
                </form>

                <form method="POST" action="{{ route('produk.addToCart', ['id' => $produk->id]) }}" id="formTambah">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $produk->id }}">
                    <input type="hidden" name="qty" id="formTambahQty">
                    <button type="submit" class="btn btn-success px-4 py-2">Tambahkan ke Keranjang</button>
                </form>
            @else
                <form method="POST" action="{{ route('produk.tambahWishlist', ['id' => $produk->id]) }}">
                    @csrf
                    <button type="submit" class="btn btn-warning px-4 py-2">Tambah ke Wishlist</button>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection
