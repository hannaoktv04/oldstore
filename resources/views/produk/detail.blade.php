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
    $gallery = $produk->gallery;
    @endphp

    <div class="row g-5">
        <div class="col-md-6">
            <div class="mb-3 d-flex justify-content-center position-relative w-75">
                @php
                $stokHabis = $produk->stocks->qty == 0;
                @endphp
                @if($stokHabis)
                <div class="position-absolute top-50 start-50 translate-middle d-flex justify-content-center align-items-center"
                    style="background-color: rgba(0, 0, 0, 0.6);
                            width: 150px; height: 150px;
                            border-radius: 50%;">
                    <span class="text-white fs-5 fw-bold">Habis</span>
                </div>
                @endif
                <img id="mainImage" src="{{ asset('storage/' . $gallery->first()) }}" 
                    style="width: 100%; height: 350px; object-fit: cover; object-position: center;"
                    class="img-fluid rounded-4 shadow-sm w-100" alt="{{ $produk->nama_barang }}">
            </div>

            <div class="d-flex gap-2 overflow-auto">
                @foreach($gallery as $path)
                <div class="flex-shrink-0">
                    <img src="{{ asset('storage/' . $path) }}" data-full="{{ asset('storage/' . $path) }}"
                        class="img-thumbnail rounded-3 thumbnail-click"
                        style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;">
                </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-6">
            <span class="text-black fw-bold d-block mb-1 fs-6">{{ $produk->category->categori_name ?? 'Kategori Tidak
                Diketahui' }}</span>
            <h3 class="fw-bold mb-3"> {{ $produk->nama_barang }}</h3>

            <div class="deskripsi-item text-muted mb-4">
                <p class="fw-bold text-black mb-1">Detail produk</p>
                <p class="text-black" style="line-height: 1.6;"> {!! nl2br(e($produk->deskripsi)) !!}</p>
            </div>

            <div class="mb-3 ">
                <label for="qty " class="form-label fw-bold">Jumlah</label>
                <div class="input-group btn-outline-success"
                    style="width: fit-content; border: 1px solid #198754; border-radius: 50px; overflow: hidden;">
                    <button type="button" class="btn btn-outline-success px-3 py-1 border-0 qty-btn bg-white"
                        onclick="ubahQty(-1)">-</button>
                    <input type="number" id="qty" value="1" min="1" max="{{ $produk->stocks->qty }}"
                        class="form-control text-center border-0 bg-white"
                        style="max-width: 45px; height: 40px; line-height: 1; padding: 0 0.25rem;">
                    <button type="button" class="btn btn-outline-success px-3 py-1 border-0 qty-btn bg-white"
                        onclick="ubahQty(1)">+</button>
                </div>
                <div id="qtyAlertContainer" class="mt-2"></div>
            </div>
            <p class="mb-5"> <span class="fw-bold text-black">{{ $produk->stocks->qty }}</span> produk tersedia</p>
            <div class="d-flex gap-3">
                @if ($produk->stocks->qty > 0)
                <form method="POST" action="{{ route('user.wishlist', ['id' => $produk->id]) }}">
                    @csrf
                    <button type="button" id="heart-icon" class="icon-button text-dark bg-transparent border-0 p-1 "
                        data-bs-toggle="modal" data-bs-target="#wishlistModal">
                        <i class="bi bi-heart fs-4"></i>
                    </button>
                </form>
                <form method="POST" action="{{ route('produk.pesanLangsung', ['id' => $produk->id]) }}" id="formPesan">
                    @csrf
                    <input type="hidden" name="qty" id="formPesanQty">
                    <button type="button" class="btn btn-outline-success px-4 py-2" data-bs-toggle="modal"
                        data-bs-target="#modalTanggalLangsung" id="btnPesanLangsung">
                        Pesan Langsung
                    </button>
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
                    <button type="button" class="btn btn-outline-danger px-4 py-2" data-bs-toggle="modal"
                        data-bs-target="#wishlistModal">
                        <i class="bi bi-heart me-2"></i>Tambah ke Wishlist
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalTanggalLangsung" tabindex="-1" aria-labelledby="modalTanggalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('produk.pesanLangsung', ['id' => $produk->id]) }}" id="formPesanLangsung">
            @csrf
            @php
                $now = \Carbon\Carbon::now()->format('Y-m-d\TH:i');
            @endphp
            <input type="hidden" name="qty" id="formPesanQtyFinal">
            <input type="hidden" name="tanggal_pengiriman" id="tanggalPengambilanLangsung">
            <div class="modal-content">
                <div class="modal-header border-0 justify-content-center">
                    <h5 class="modal-title" id="modalTanggalLabel">Pilih Tanggal Pengiriman</h5>
                </div>
                <div class="modal-body border-0">
                    <input type="text" class="form-control" id="tanggalPickerLangsung" value="{{ $now }}"
                        required>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-outline-success px-4 py-1"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success d-flex align-items-center py-1 gap-2"
                        id="btnKirimPermintaan">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"
                            id="spinnerKirim"></span>
                        <span id="textKirim">Kirim Permintaan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="wishlistModal" tabindex="-1" aria-labelledby="wishlistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('user.wishlist.store', ['id' => $produk->id]) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-0 justify-content-center">
                    <h5 class="modal-title" id="wishlistModalLabel">Add to Wishlist</h5>
                </div>
                <div class="modal-body border-0">
                    <p><strong>Stok tersedia:</strong> {{ $produk->stocks->qty }}</p>
                    <div class="mb-3">
                        <label for="qtyWishlist" class="form-label">Jumlah yang Dibutuhkan</label>
                        <input type="number" class="form-control" name="qty" id="qtyWishlist" min="1" value="1"
                            required>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Tambah ke Wishlist</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
