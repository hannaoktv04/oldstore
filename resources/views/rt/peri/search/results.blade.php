@extends('peri::layouts.app')

@section('content')
<div class="container py-4">
  <h4 class="mb-4">Hasil Pencarian untuk : <strong>{{ $keyword }}</strong></h4>

  @if($produk->isEmpty())
    <p class="text-muted">Tidak ada barang ditemukan.</p>
  @else
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
      @foreach($produk as $item)
        @php
          $stokHabis = ($item->stocks->qty ?? 0) == 0;
          $nonaktif = $opnameAktif ?? false;
        @endphp

        <div class="col position-relative">
          <a href="{{ $nonaktif ? '#' : route('produk.show', ['id' => $item->id]) }}"
             class="text-decoration-none {{ $nonaktif ? 'text-muted disabled-link' : ($stokHabis ? 'text-muted' : 'text-dark') }}"
             style="{{ $nonaktif ? 'pointer-events: none;' : '' }}">

            <div class="card h-100 card-3d shadow-sm position-relative {{ $nonaktif ? 'bg-light grayscale-card' : ($stokHabis ? 'bg-light' : '') }}"
                 style="{{ $stokHabis || $nonaktif ? 'opacity: 0.7;' : '' }}">

              @if($stokHabis)
                <div class="position-absolute top-0 end-0 m-2" style="z-index: 2;">
                  <span class="badge bg-danger">Stok Habis</span>
                </div>
              @endif

              <img src="{{ asset('storage/' . ($item->photo?->image ?? 'placeholder.jpg')) }}"
                   class="card-img-top" style="height: 160px; object-fit: cover;" alt="{{ $item->nama_barang }}">

              <div class="card-body py-2 px-3">
                <h6 class="card-title mb-1" style="font-size: 14px;">{{ $item->nama_barang }}</h6>
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
      @endforeach
    </div>
  @endif
</div>
@endsection
