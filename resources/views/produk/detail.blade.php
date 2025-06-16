@extends('layouts.app')

@section('content')
<div class="container py-3">
    {{-- Breadcrumb --}}
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
        {{-- Kolom Gambar --}}
        <div class="col-md-6">
            {{-- Gambar Utama --}}
            <div class="mb-3">
                <img id="mainImage"
                     src="{{ asset('storage/' . $gallery->first()) }}"
                     class="img-fluid rounded-4 shadow-sm w-75"
                     alt="{{ $produk->nama_barang }}">
            </div>

            {{-- Thumbnail --}}
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

        {{-- Kolom Informasi Produk --}}
        <div class="col-md-6">
            <span class="text-black fw-bold d-block mb-1 fs-6">{{ $produk->category->categori_name ?? 'Kategori Tidak Diketahui' }}</span>
            <h3 class="fw-bold mb-3"> {{ $produk->nama_barang }}</h3>

            <div class="deskripsi-item text-muted mb-4">
                <p class="fw-bold text-black mb-1">Detail produk</p>
                <p class="text-black" style="line-height: 1.6;"> {!! nl2br(e($produk->deskripsi)) !!}</p>
            </div>

            <div class="mb-3 ">
                <label for="qty " class="form-label fw-bold">Jumlah</label>
                <div class="input-group" style="width: 140px;">
                    <button type="button" class="btn btn-outline-secondary" onclick="ubahQty(-1)">-</button>
                    <input type="number" id="qty" value="1" min="1" max="{{ $produk->stok_minimum }}" class="form-control text-center">
                    <button type="button" class="btn btn-outline-secondary" onclick="ubahQty(1)">+</button>
                </div>
                <div id="qtyAlertContainer" class="mt-2"></div>
            </div>
            <p class="mb-5"> <span class="fw-bold text-black">{{ $produk->stok_minimum }}</span> produk tersedia</p>
            <div class="d-flex gap-3">
                @if ($produk->stok_minimum > 0)
                    {{-- Tombol Pesan Langsung --}}
                    <form method="POST" action="{{ route('produk.pesanLangsung', ['id' => $produk->id]) }}" id="formPesan">
                        @csrf
                        <input type="hidden" name="qty" id="formPesanQty">
                        <button type="submit" class="btn btn-outline-secondary px-4 py-2">Pesan Langsung</button>
                    </form>

                    {{-- Tombol Tambah ke Keranjang --}}
                    <form method="POST" action="{{ route('produk.addToCart', ['id' => $produk->id]) }}" id="formTambah">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $produk->id }}">
                        <input type="hidden" name="qty" id="formTambahQty">
                        <button type="submit" class="btn btn-success px-4 py-2">Tambahkan ke Keranjang</button>
                    </form>
                @else
                    <div class="alert alert-warning">Stok produk habis. Anda bisa menambahkan ke wishlist.</div>
                    <form method="POST" action="{{ route('produk.wishlist', ['id' => $produk->id]) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning px-4 py-2">Tambah ke Wishlist</button>
                    </form>
                @endif
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
