@extends('layouts.app')

@section('content')

<div class="container">
    <div class="position-relative mb-5 overflow-hidden rounded-4 hero-wrapper" style="height: 400px;">
        <img src="{{ asset('assets/img/hero.jpg') }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Hero Image">
        
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>

        <div class="position-absolute top-50 start-0 translate-middle-y ps-8 text-white hero-content fade-in">
            <h1 class="fw-bold display-5 mb-3">Selamat Datang</h1>
            <p class="lead mb-4">Temukan barang yang paling sering dibutuhkan dan ajukan permintaan sekarang juga.</p>
            <a href="{{ route('kategori.index') }}" class="btn fw-bold text-white px-4 py-2" style="background-color: #2aa7a7;">Ajukan Sekarang</a>
        </div>
    </div>
</div>

<section class="container">
    <h4 class="mb-4 fw-semibold">Paling Banyak Dipesan</h4>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 row-cols-lg-6 g-3">
        @forelse ($items as $item)
            <div class="col">
                @php
                    $stokHabis = $item->stok_minimum == 0;
                @endphp

                <a href="{{ route('produk.show', ['id' => $item->id]) }}" class="text-decoration-none {{ $stokHabis ? 'text-muted' : 'text-dark' }}">
                    <div class="card h-100 card-3d shadow-sm position-relative {{ $stokHabis ? 'bg-light' : '' }}" style="{{ $stokHabis ? 'opacity: 0.6;' : '' }}">
                        @if($stokHabis)
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-danger">Stok Habis</span>
                            </div>
                        @endif

                        <img src="{{ asset('storage/' . ($item->photo?->image ?? 'placeholder.jpg')) }}" class="card-img-top" style="height: 160px; object-fit: cover;" alt="{{ $item->nama_barang }}">

                        <div class="card-body">
                            <h6 class="card-title">{{ $item->nama_barang }}</h6>
                            <p class="card-text small text-muted mb-1">{{ $item->category?->categori_name ?? '-' }}</p>
                            <p class="card-text">
                                @if ($stokHabis)
                                    <span class="text-danger">0 Tersisa</span>
                                @else
                                    <strong>{{ number_format($item->stok_minimum, 0) }}</strong> Tersisa
                                @endif
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <p class="text-muted">Belum ada data pemesanan.</p>
        @endforelse
    </div>
</section>

@endsection
