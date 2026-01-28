@extends('peri::layouts.app')

@section('content')
<div class="container py-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
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
                $stokHabis = ($produk->stok == 0);
                @endphp
                @if($stokHabis)
                <div class="position-absolute top-50 start-50 translate-middle d-flex justify-content-center align-items-center"
                    style="background-color: rgba(0, 0, 0, 0.6); width: 150px; height: 150px; border-radius: 50%;">
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
            <span class="text-black fw-bold d-block mb-1 fs-6">{{ $produk->category->categori_name ?? 'Kategori Tidak Diketahui' }}</span>
            <h3 class="fw-bold mb-3"> {{ $produk->nama_barang }}</h3>
            <p class="fw-bold mb-3" style="font-size: 20px;">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
            
            <div class="deskripsi-item text-muted mb-4">
                <p class="fw-bold text-black mb-1">Detail produk</p>
                <p class="text-black" style="line-height: 1.6;"> {!! nl2br(e($produk->deskripsi)) !!}</p>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Pilih Ukuran</label>
                <div class="d-flex gap-2 flex-wrap">
                    @forelse($produk->sizes as $s)
                        <input type="radio" class="btn-check" name="size_selection" id="size_{{ $s->id }}" value="{{ $s->size }}">
                        <label class="btn btn-outline-success rounded-pill px-3" for="size_{{ $s->id }}">
                            {{ $s->size }}
                        </label>
                    @empty
                        <span class="text-muted small">Ukuran tidak tersedia</span>
                    @endforelse
                </div>
                <div id="sizeAlertContainer" class="mt-2"></div>
            </div>

            <div class="mb-3">
                <label for="qty" class="form-label fw-bold">Jumlah</label>
                <div class="input-group" style="width: fit-content; border: 1px solid #a19d53ff; border-radius: 50px; overflow: hidden;">
                    <button type="button" class="btn btn-outline-success px-3 py-1 border-0 bg-white" onclick="ubahQty(-1)">-</button>
                    <input type="number" id="qty" value="1" min="1" max="{{ $produk->stok }}"
                        class="form-control text-center border-0 bg-white"
                        style="max-width: 45px; height: 40px; line-height: 1; padding: 0 0.25rem;">
                    <button type="button" class="btn btn-outline-success px-3 py-1 border-0 bg-white" onclick="ubahQty(1)">+</button>
                </div>
                <div id="qtyAlertContainer" class="mt-2"></div>
            </div>

            <p class="mb-5"> <span class="fw-bold text-black">{{ $produk->stok }}</span> produk tersedia</p>

            <div class="d-flex gap-3">
                @if ($produk->stok > 0)
                    <form method="POST" action="{{ route('produk.pesanLangsung', ['id' => $produk->id]) }}" id="formPesan">
                        @csrf
                        <input type="hidden" name="qty" id="formPesanQty">
                        <input type="hidden" name="size" id="formPesanSize">
                        <button type="submit" class="btn btn-outline-success px-4 py-2" id="btnPesanLangsung">
                            Pesan Langsung
                        </button>
                    </form>

                    <form method="POST" action="{{ route('produk.addToCart', ['id' => $produk->id]) }}" id="formTambah">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $produk->id }}">
                        <input type="hidden" name="qty" id="formTambahQty">
                        <input type="hidden" name="size" id="formTambahSize">
                        <button type="submit" class="btn btn-success px-4 py-2">Tambahkan ke Keranjang</button>
                    </form>
                @else
                    <button type="button" class="btn btn-outline-danger px-4 py-2" disabled>Stok Habis</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/peri/detail.js') }}"></script>
@endpush