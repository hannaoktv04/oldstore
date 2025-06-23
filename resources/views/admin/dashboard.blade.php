@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-9">
            <div class="row g-3">
                <div class="col-sm-6 col-lg-3.5">
                    <a href="{{ route('admin.pengajuan.status', 'submitted') }}"
                       class="card text-decoration-none text-dark shadow-sm hover-3d border-0 bg-light h-100">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                            <div class="mb-2 text-success">
                                <i class="bi bi-send-check-fill display-4"></i>
                            </div>
                            <h6 class="text-muted mb-1">Pengajuan Baru</h6>
                            <h2 class="fw-bold">{{ $pengajuanBaru }}</h2>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3.5">
                    <a href="{{ route('admin.pengajuan.status', 'approved') }}"
                       class="card text-decoration-none text-dark shadow-sm hover-3d border-0 bg-light h-100">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                            <div class="mb-2 text-warning">
                                <i class="bi bi-truck display-4"></i>
                            </div>
                            <h6 class="text-muted mb-1">Perlu Dikirim</h6>
                            <h2 class="fw-bold">{{ $perluDikirim }}</h2>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3.5">
                    <a href="{{ route('admin.pengajuan.status', 'delivered') }}"
                       class="card text-decoration-none text-dark shadow-sm hover-3d border-0 bg-light h-100">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                            <div class="mb-2 text-info">
                                <i class="bi bi-box-seam display-4"></i>
                            </div>
                            <h6 class="text-muted mb-1">Konfirmasi Pengiriman</h6>
                            <h2 class="fw-bold">{{ $pengajuanSelesai }}</h2>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3.5">
                    <a href="{{ route('admin.pengajuan.status', 'rejected') }}"
                       class="card text-decoration-none text-dark shadow-sm hover-3d border-0 bg-light h-100">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                            <div class="mb-2 text-danger">
                                <i class="bi bi-x-circle-fill display-4"></i>
                            </div>
                            <h6 class="text-muted mb-1">Ditolak</h6>
                            <h2 class="fw-bold">{{ $pembatalan }}</h2>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
