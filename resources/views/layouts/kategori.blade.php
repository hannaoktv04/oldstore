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
                <li class="list-group-item">Alat Tulis</li>
                <li class="list-group-item">Penjepit Kertas</li>
                <li class="list-group-item">Penghapus/Korektor</li>
                <li class="list-group-item">Buku Tulis</li>
                <li class="list-group-item">Ordner & Map</li>
                <li class="list-group-item">Penggaris</li>
                <li class="list-group-item">Cutter (Alat Tulis Kantor)</li>
                <li class="list-group-item">Alat Perekat</li>
                <li class="list-group-item">Barang Cetakan</li>
                <li class="list-group-item">USB/Flash Disk</li>
                <li class="list-group-item">Lainnya...</li>
            </ul>
        </div>

        <div class="col-md-9">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card h-100 card-3d shadow-sm">
                        <img src="{{ asset('assets/img/products/sup-game-box-400.png') }}" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h6 class="card-title">Nama Produk</h6>
                            <p class="card-text small text-muted mb-1">Kategori</p>
                            <p class="card-text"><strong>469</strong> Tersisa</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <a href="{{ route('produk.detail', ['id' => 1]) }}" class="text-decoration-none">
                        <div class="card h-100 card-3d shadow-sm">
                            <img src="{{ asset('assets/img/products/samsung-watch-4.png') }}" class="card-img-top" alt="Pulpen">
                            <div class="card-body">
                                <h6 class="card-title">Pulpen</h6>
                                <p class="card-text small text-muted mb-1">ATK</p>
                                <p class="card-text"><strong>100</strong> Tersisa</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <div class="card h-100 card-3d shadow-sm">
                        <img src="{{ asset('assets/img/products/samsung-s22.png') }}" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h6 class="card-title">Nama Produk</h6>
                            <p class="card-text small text-muted mb-1">Kategori</p>
                            <p class="card-text"><strong>469</strong> Tersisa</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 card-3d shadow-sm">
                        <img src="{{ asset('assets/img/products/nintendo-switch.png') }}" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h6 class="card-title">Nama Produk</h6>
                            <p class="card-text small text-muted mb-1">Kategori</p>
                            <p class="card-text"><strong>469</strong> Tersisa</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 card-3d shadow-sm">
                        <img src="{{ asset('assets/img/products/sup-game-box-400.png') }}" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h6 class="card-title">Nama Produk</h6>
                            <p class="card-text small text-muted mb-1">Kategori</p>
                            <p class="card-text"><strong>469</strong> Tersisa</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 card-3d shadow-sm">
                        <img src="{{ asset('assets/img/products/samsung-watch-4.png') }}" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h6 class="card-title">Nama Produk</h6>
                            <p class="card-text small text-muted mb-1">Kategori</p>
                            <p class="card-text"><strong>469</strong> Tersisa</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 card-3d shadow-sm">
                        <img src="{{ asset('assets/img/products/samsung-s22.png') }}" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h6 class="card-title">Nama Produk</h6>
                            <p class="card-text small text-muted mb-1">Kategori</p>
                            <p class="card-text"><strong>469</strong> Tersisa</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 card-3d shadow-sm">
                        <img src="{{ asset('assets/img/products/nintendo-switch.png') }}" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h6 class="card-title">Nama Produk</h6>
                            <p class="card-text small text-muted mb-1">Kategori</p>
                            <p class="card-text"><strong>469</strong> Tersisa</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 card-3d shadow-sm">
                        <img src="{{ asset('assets/img/products/sup-game-box-400.png') }}" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h6 class="card-title">Nama Produk</h6>
                            <p class="card-text small text-muted mb-1">Kategori</p>
                            <p class="card-text"><strong>469</strong> Tersisa</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 card-3d shadow-sm">
                        <img src="{{ asset('assets/img/products/samsung-watch-4.png') }}" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h6 class="card-title">Nama Produk</h6>
                            <p class="card-text small text-muted mb-1">Kategori</p>
                            <p class="card-text"><strong>469</strong> Tersisa</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 card-3d shadow-sm">
                        <img src="{{ asset('assets/img/products/samsung-s22.png') }}" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h6 class="card-title">Nama Produk</h6>
                            <p class="card-text small text-muted mb-1">Kategori</p>
                            <p class="card-text"><strong>469</strong> Tersisa</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 card-3d shadow-sm">
                        <img src="{{ asset('assets/img/products/nintendo-switch.png') }}" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h6 class="card-title">Nama Produk</h6>
                            <p class="card-text small text-muted mb-1">Kategori</p>
                            <p class="card-text"><strong>469</strong> Tersisa</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
