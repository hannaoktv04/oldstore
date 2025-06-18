@extends('layouts.app')

@section('title', 'Kategori Barang')

@section('content')

<div class="container">
    <div class="position-relative mb-3 overflow-hidden rounded-4 hero-wrapper" style="height: 200px;">
        <img src="{{ asset('assets/img/hero.jpg') }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Hero Image">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>
        <div class="position-absolute top-50 start-0 translate-middle-y text-white ps-5 hero-content fade-in">
            <h4 class="fw-semibold display-6 mb-3">Kategori Barang</h4>
            <p class="lead mb-0">PERI / Kategori Barang</p>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <h5 class="fw-bold">Kategori Barang</h5>
            <ul class="list-group list-group-flush">
                @foreach ($categories as $category)
                     <li class="list-group-item">{{ $category->categori_name }}</li>
                @endforeach
            </ul>
        </div>

        <div class="col-md-9">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                @foreach ($items as $item)
                @php
                    $stokHabis = $item->stok_minimum == 0;
                @endphp
                <div class="col">
                    <a href="{{ route('produk.show', ['id' => $item->id]) }}"
                       class="text-decoration-none {{ $stokHabis ? 'text-muted' : 'text-dark' }}">
                        <div class="card h-100 card-3d shadow-sm position-relative {{ $stokHabis ? 'bg-light' : '' }}" style="{{ $stokHabis ? 'opacity: 0.6;' : '' }}">
                            @if($stokHabis)
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-danger">Stok Habis</span>
                                </div>
                            @endif

                            <img src="{{ asset('storage/' . ($item->photo?->image ?? 'placeholder.jpg')) }}"
                                 class="card-img-top"
                                 style="height: 160px; object-fit: cover;"
                                 alt="{{ $item->nama_barang }}">

                            <div class="card-body">
                                <h6 class="card-title">{{ $item->nama_barang }}</h6>
                                <p class="card-text small text-muted mb-1">
                                    {{ $item->category?->categori_name ?? '-' }}
                                </p>
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
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
