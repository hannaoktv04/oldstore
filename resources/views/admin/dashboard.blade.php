@extends('layouts.app')

@section('content')

<div class="container py-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <h5 class="fw-bold">Kategori Barang</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item bi-clipboard2-check-fill">Daftar Barang</li>
                <li class="list-group-item">Cek Pengajuan</li>
                <li class="list-group-item">CRUD Barang</li>
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
