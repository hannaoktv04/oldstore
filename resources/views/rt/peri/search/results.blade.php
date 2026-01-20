@extends('peri::layouts.app')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-success">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pencarian</li>
        </ol>
    </nav>

    <h4 class="mb-4">Hasil Pencarian untuk: <span class="text-success">"{{ $keyword }}"</span></h4>

    @if($produk->isEmpty())
        <div class="text-center py-5">
            <i class="ri-search-line display-1 text-muted"></i>
            <h5 class="mt-3 text-secondary">Ups! Produk "{{ $keyword }}" tidak ditemukan.</h5>
            <p class="text-muted">Coba gunakan kata kunci lain atau periksa ejaan Anda.</p>
            <a href="{{ route('home') }}" class="btn btn-success rounded-pill px-4 mt-2">Kembali Belanja</a>
        </div>
    @else
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
            @foreach($produk as $item)
                @php
                    // Menggunakan field 'stok' sesuai standar CartController
                    $stokQty = $item->stok ?? 0; 
                    $stokHabis = ($stokQty <= 0);
                    $nonaktif = $opnameAktif ?? false;
                @endphp

                <div class="col">
                    <a href="{{ $nonaktif ? '#' : route('produk.show', ['id' => $item->id]) }}"
                       class="text-decoration-none {{ $nonaktif ? 'disabled-link' : '' }}"
                       style="{{ $nonaktif ? 'pointer-events: none;' : '' }}">

                        <div class="card h-100 card-3d shadow-sm border-0 position-relative {{ $nonaktif ? 'bg-light grayscale-card' : '' }}"
                             style="border-radius: 12px; overflow: hidden; transition: 0.3s;">

                            {{-- Label Status --}}
                            @if($stokHabis)
                                <div class="position-absolute top-0 end-0 m-2" style="z-index: 2;">
                                    <span class="badge bg-danger rounded-pill small">Habis</span>
                                </div>
                            @elseif($nonaktif)
                                <div class="position-absolute top-0 end-0 m-2" style="z-index: 2;">
                                    <span class="badge bg-warning text-dark rounded-pill small">Opname</span>
                                </div>
                            @endif

                            {{-- Gambar Produk --}}
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . ($item->photo_url ?? 'placeholder.jpg')) }}"
                                     class="card-img-top {{ $stokHabis || $nonaktif ? 'opacity-50' : '' }}" 
                                     style="height: 160px; object-fit: cover;" 
                                     alt="{{ $item->nama_barang }}">
                            </div>

                            <div class="card-body py-2 px-3">
                                <h6 class="card-title mb-1 text-dark text-truncate" style="font-size: 14px;" title="{{ $item->nama_barang }}">
                                    {{ $item->nama_barang }}
                                </h6>
                                <p class="card-text small text-muted mb-1">{{ $item->category?->categori_name ?? '-' }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="fw-bold text-success" style="font-size: 13px;">
                                        Rp {{ number_format($item->harga ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                
                                <p class="card-text mb-0 mt-1" style="font-size: 11px;">
                                    @if ($stokHabis)
                                        <span class="text-danger fw-bold">Stok Habis</span>
                                    @else
                                        <span class="text-muted">Tersisa: </span>
                                        <strong class="text-dark">{{ number_format($stokQty, 0) }}</strong>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .card-3d:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .grayscale-card {
        filter: grayscale(100%);
        opacity: 0.7;
    }
    .disabled-link {
        cursor: not-allowed;
    }
</style>
@endsection