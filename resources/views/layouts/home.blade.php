@extends('layouts.app')

@section('content')

<div class="container">
  <div class="position-relative mb-5 overflow-hidden rounded-4 hero-wrapper" style="height: 400px;">
    <img src="{{ asset('assets/img/hero.jpg') }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Hero Image">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>

    <div class="position-absolute top-50 start-0 translate-middle-y ps-4 ps-sm-5 text-white hero-content fade-in">
      <h1 class="fw-bold display-6 mb-2">Selamat Datang</h1>
      <p class="lead mb-3">Temukan barang yang paling sering dibutuhkan dan ajukan permintaan sekarang juga.</p>
      <a href="{{ route('kategori.index') }}" class="btn fw-bold text-white px-4 py-2" style="background-color: #2aa7a7;">Ajukan Sekarang</a>
    </div>
  </div>
</div>

<section class="container">
  <h5 class="mb-4 fw-semibold">Paling Banyak Dipesan</h5>
  <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3">
    @forelse ($items as $item)
      @php
        $stokHabis = ($item->stocks->qty ?? 0) == 0;
        $nonaktif = $opnameAktif ?? false;
      @endphp

      <div class="col">
        <a href="{{ $nonaktif ? '#' : route('produk.show', ['id' => $item->id]) }}"
           class="text-decoration-none {{ $nonaktif ? 'text-muted disabled-link' : ($stokHabis ? 'text-muted' : 'text-dark') }}"
           style="{{ $nonaktif ? 'pointer-events: none;' : '' }}">
          <div class="card h-100 card-3d shadow-sm position-relative {{ $nonaktif ? 'bg-light grayscale-card' : ($stokHabis ? 'bg-light' : '') }}"
               style="{{ $stokHabis || $nonaktif ? 'opacity: 0.6;' : '' }}">

            @if($stokHabis)
              <div class="position-absolute top-0 end-0 m-2">
                <span class="badge bg-danger">Stok Habis</span>
              </div>
            @endif

            <img src="{{ asset('storage/' . ($item->photo?->image ?? 'placeholder.jpg')) }}"
                 class="card-img-top rounded-top" alt="{{ $item->nama_barang }}"
                 style="height: 160px; object-fit: cover;">

            <div class="card-body py-2 px-3">
              <h6 class="card-title mb-1" style="font-size: 14px">{{ $item->nama_barang }}</h6>
              <p class="card-text small text-muted mb-1">{{ $item->category?->categori_name ?? '-' }}</p>
              <p class="card-text mb-0" style="font-size: 13px;">
                @if ($stokHabis)
                  <span class="text-danger">0 Tersisa</span>
                @else
                  <strong>{{ number_format($item->stocks->qty, 0) }}</strong> Tersisa
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
