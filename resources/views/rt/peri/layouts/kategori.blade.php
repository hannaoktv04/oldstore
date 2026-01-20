@extends('peri::layouts.app')

@section('title', 'Kategori Barang')

@section('content')
<div class="container">
  <div class="position-relative mb-3 overflow-hidden rounded-4" style="height: 200px;">
    <img src="{{ asset('assets/img/aneka sepatu.jpeg') }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Hero Image">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>
    <div class="position-absolute top-50 start-0 translate-middle-y text-white ps-5">
      <h4 class="fw-semibold display-6 mb-3">Kategori Barang</h4>
      <p class="lead mb-0"> OldStore / Kategori Barang</p>
    </div>
  </div>

  <div class="d-block d-md-none mb-3 position-relative z-3">
    <button class="btn border-0 fw-bold px-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#kategoriMobile" aria-controls="kategoriMobile">
      Kategori <i class="bi bi-chevron-right"></i>
    </button>
  </div>

  <div class="offcanvas offcanvas-start custom-kategori-offcanvas" tabindex="-1" id="kategoriMobile" aria-labelledby="kategoriMobileLabel">
    <div class="offcanvas-header py-2 px-3 border-bottom">
      <h6 class="offcanvas-title fw-bold mb-0" id="kategoriMobileLabel">Kategori</h6>
      <button type="button" class="btn border-0" data-bs-dismiss="offcanvas" aria-label="Close">
        <i class="bi bi-chevron-left fs-5"></i>
      </button>
    </div>
    <div class="offcanvas-body p-0 overflow-auto">
      <ul class="list-group list-group-flush">
        @foreach ($categories as $category)
          <li class="list-group-item small px-3 py-2">
            <a href="{{ route('kategori.show', $category->id) }}" class="text-decoration-none d-flex justify-content-between align-items-center w-100">
              {{ $category->categori_name }}
              00<span class="badge bg-custom rounded-pill">{{ $category->items_count }}</span>
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3 d-none d-md-block">
      <h5 class="fw-bold mb-3">Kategori Barang</h5>
      <ul class="list-group list-group-flush">
        @foreach ($categories as $category)
          <li class="list-group-item px-3 py-2">
            <a href="{{ route('kategori.show', $category->id) }}"
              class="nav-link text-decoration-none d-flex justify-content-between align-items-center w-100
                      {{ request()->routeIs('kategori.show') && request()->route('id') == $category->id ? 'text-success fw-semibold' : 'text-dark' }}">
                {{ $category->categori_name }}
                <span class="badge bg-custom rounded-pill">{{ $category->items_count }}</span>
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    <div class="col-md-9">
      <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
        @forelse ($items as $item)
          @php
            $stokHabis = $stokHabis = ($item->stok ?? 0) == 0;
            $nonaktif = $opnameAktif ?? false;
          @endphp
          <div class="col">
            <a href="{{ $nonaktif ? '#' : route('produk.show', ['id' => $item->id]) }}"
               class="text-decoration-none {{ $nonaktif ? 'text-muted disabled-link' : ($stokHabis ? 'text-muted' : 'text-dark') }}"
               style="{{ $nonaktif ? 'pointer-events: none;' : '' }}">
              <div class="card card-3d h-100 shadow-sm position-relative {{ $nonaktif ? 'bg-light grayscale-card' : ($stokHabis ? 'bg-light' : '') }}"
                   style="{{ $stokHabis ? 'opacity: 0.6;' : '' }}">

                @if($stokHabis)
                  <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-danger">Stok Habis</span>
                  </div>
                @endif

                <img src="{{ asset('storage/' . ($item->photo?->image ?? 'placeholder.jpg')) }}"
                     class="card-img-top" style="height: 160px; object-fit: cover;" alt="{{ $item->nama_barang }}">

                <div class="card-body">
                  <h6 class="card-title">{{ $item->nama_barang }}</h6>
                  <p class="card-text small text-muted mb-1">{{ $item->category?->categori_name ?? '-' }}</p>
                  <p class="card-text mb-1" style="font-size: 14px;">
                    <strong>Rp {{ number_format($item->harga, 0, ',', '.') }}</strong>
                  </p>
                  <p class="card-text">
                    @if ($stokHabis)
                      <span class="text-danger">0 Tersisa</span>
                    @else
                      <strong>{{ number_format($item->stok ?? 0, 0) }}</strong> Tersisa
                    @endif
                  </p>
                </div>
              </div>
            </a>
          </div>
        @empty
          <div class="col-12">
            <div class="alert alert-info">Belum ada produk dalam kategori ini.</div>
          </div>
        @endforelse
      </div>

      <div class="d-flex justify-content-center mt-4">
        {{ $items->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
</div>
@endsection