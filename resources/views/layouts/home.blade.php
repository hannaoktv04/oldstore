@extends('layouts.app')

@section('content')

<div class="container">
    <div class="position-relative mb-5 overflow-hidden rounded-4 hero-wrapper" style="height: 400px;">
        <img src="{{ asset('assets/img/hero.jpg') }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Hero Image">
        
        <!-- Overlay -->
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"></div>

        <!-- Konten -->
        <div class="position-absolute top-50 start-0 translate-middle-y ps-8 text-white hero-content fade-in">
            <h1 class="fw-bold display-5 mb-3">Headline</h1>
            <p class="lead mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit.<br> 
                Quisque varius gravida ligula rutrum gravida.</p>
            <a href="#" class="btn fw-bold text-white px-4 py-2" style="background-color: #2aa7a7;">Ajukan Sekarang</a>
        </div>
    </div>
</div>


<section class="container">
    <h4 class="mb-12 fw-semibold">Paling Banyak Dipesan</h4>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <div class="col">
            <div class="card h-100 card-3d">
                <img src="{{ asset('assets/img/products/xbox-series-x.png') }}" class="card-img-top" alt="Produk">
                <div class="card-body">
                    <h5 class="card-title">Nama Produk</h5>
                    <p class="card-text">Kategori</p>
                    <p><strong>469</strong> Tersisa</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 card-3d">
                <img src="{{ asset('assets/img/products/apple-iMac-4k.png') }}" class="card-img-top" alt="Produk">
                <div class="card-body">
                    <h5 class="card-title">Nama Produk</h5>
                    <p class="card-text">Kategori</p>
                    <p><strong>469</strong> Tersisa</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 card-3d">
                <img src="{{ asset('assets/img/products/amazon-echo-dot.png') }}" class="card-img-top" alt="Produk">
                <div class="card-body">
                    <h5 class="card-title">Nama Produk</h5>
                    <p class="card-text">Kategori</p>
                    <p><strong>469</strong> Tersisa</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 card-3d">
                <img src="{{ asset('assets/img/products/xbox-series-x.png') }}" class="card-img-top" alt="Produk">
                <div class="card-body">
                    <h5 class="card-title">Nama Produk</h5>
                    <p class="card-text">Kategori</p>
                    <p><strong>469</strong> Tersisa</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 card-3d">
                <img src="{{ asset('assets/img/products/apple-iMac-4k.png') }}" class="card-img-top" alt="Produk">
                <div class="card-body">
                    <h5 class="card-title">Nama Produk</h5>
                    <p class="card-text">Kategori</p>
                    <p><strong>469</strong> Tersisa</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 card-3d">
                <img src="{{ asset('assets/img/products/amazon-echo-dot.png') }}" class="card-img-top" alt="Produk">
                <div class="card-body">
                    <h5 class="card-title">Nama Produk</h5>
                    <p class="card-text">Kategori</p>
                    <p><strong>469</strong> Tersisa</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 card-3d">
                <img src="{{ asset('assets/img/products/xbox-series-x.png') }}" class="card-img-top" alt="Produk">
                <div class="card-body">
                    <h5 class="card-title">Nama Produk</h5>
                    <p class="card-text">Kategori</p>
                    <p><strong>469</strong> Tersisa</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 card-3d">
                <img src="{{ asset('assets/img/products/apple-iMac-4k.png') }}" class="card-img-top" alt="Produk">
                <div class="card-body">
                    <h5 class="card-title">Nama Produk</h5>
                    <p class="card-text">Kategori</p>
                    <p><strong>469</strong> Tersisa</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 card-3d">
                <img src="{{ asset('assets/img/products/amazon-echo-dot.png') }}" class="card-img-top" alt="Produk">
                <div class="card-body">
                    <h5 class="card-title">Nama Produk</h5>
                    <p class="card-text">Kategori</p>
                    <p><strong>469</strong> Tersisa</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
