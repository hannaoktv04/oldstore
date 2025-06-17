@extends('layouts.app')

@section('content')
<div class="container py-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">PERI</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Kategori Barang</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $produk->nama_barang }}</li>
        </ol>
    </nav>

    @php
        $photo = $produk->photo;
        $gallery = collect([
            $photo->image,
            $photo->img_xl,
            $photo->img_l,
            $photo->img_m,
            $photo->img_s,
        ])->filter();
    @endphp

    <div class="row g-5">
        <div class="col-md-6">
            <div class="mb-3">
                <img id="mainImage"
                     src="{{ asset('storage/' . $gallery->first()) }}"
                     class="img-fluid rounded-4 shadow-sm w-75"
                     alt="{{ $produk->nama_barang }}">
            </div>

            <div class="d-flex gap-2 overflow-auto">
                @foreach($gallery as $path)
                    <div class="flex-shrink-0">
                        <img src="{{ asset('storage/' . $path) }}"
                             data-full="{{ asset('storage/' . $path) }}"
                             class="img-thumbnail rounded-3 thumbnail-click"
                             style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-6">
            <small class="text-muted">{{ $produk->kategori }}</small>
            <h3 class="fw-bold mt-1">{{ $produk->nama_barang }}</h3>
            <p class="text-muted mt-2">{{ $produk->deskripsi }}</p>

            <div class="mb-3">
                <label for="qty" class="form-label">Jumlah</label>
                <div class="input-group" style="width: 120px;">
                    <button type="button" class="btn btn-outline-secondary" onclick="ubahQty(-1)">-</button>
                    <input type="number" id="qty" value="1" min="1" max="{{ $produk->stok_minimum }}" class="form-control text-center">
                    <button type="button" class="btn btn-outline-secondary" onclick="ubahQty(1)">+</button>
                </div>
                <div id="qtyAlertContainer"></div>
            </div>

            <p class="text-muted">{{ $produk->stok_minimum }} produk tersedia</p>

            <div class="d-flex gap-3">
                @if ($produk->stok_minimum > 0)
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
                    <form method="POST" action="{{ route('user.wishlist', ['id' => $produk->id]) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger px-4 py-2"><i class="bi bi-heart me-2"></i>Tambah ke Wishlist</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const thumbnails = document.querySelectorAll('.thumbnail-click');
        const mainImage = document.getElementById('mainImage');

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function () {
                const fullSrc = this.getAttribute('data-full');
                mainImage.src = fullSrc;
            });
        });
    });
</script>
@endpush
