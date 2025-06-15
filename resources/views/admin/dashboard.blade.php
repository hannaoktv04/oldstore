@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <h5 class="mb-4">Admin Dashboard</h5>
        @include('components.sidebar')
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="{{ route('admin.pengajuan.status', 'submitted') }}"
                       class="card text-decoration-none text-dark shadow-sm hover-3d"
                       style="height: 160px;">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center h-100">
                            <h6>Pengajuan Baru</h6>
                            <h1 class="display-2 fw-normal">{{ $pengajuanBaru }}</h1>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('admin.pengajuan.status', 'revised') }}"
                       class="card text-decoration-none text-dark shadow-sm hover-3d"
                       style="height: 160px;">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center h-100">
                            <h6>Perubahan</h6>
                            <h1 class="display-2 fw-normal">{{ $perubahan }}</h1>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('admin.pengajuan.status', 'rejected') }}"
                       class="card text-decoration-none text-dark shadow-sm hover-3d"
                       style="height: 160px;">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center h-100">
                            <h6>Pembatalan</h6>
                            <h1 class="display-2 fw-normal">{{ $pembatalan }}</h1>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
