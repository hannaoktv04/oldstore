@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Daftar Pengajuan - Status: {{ ucfirst($status) }}</h4>

    @forelse ($pengajuans as $pengajuan)
        @php($id = $pengajuan->id)
        @php($no = str_pad($id, 3, '0', STR_PAD_LEFT))

        <div class="mb-5 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0">Pengajuan #{{ $no }}</h5>
                    <small class="text-muted">
                        Pemohon: <strong>{{ $pengajuan->user->nama ?? 'Tidak diketahui' }}</strong>
                        &middot;
                        Diajukan: {{ \Carbon\Carbon::parse($pengajuan->tanggal_permintaan)->format('d F Y') }}
                    </small>
                </div>
            </div>

            <div class="row gy-4 gx-2 align-items-center">
                {{-- Detail Barang --}}
                <div class="col-12 col-md-6 col-lg-4">
                    @foreach ($pengajuan->details as $detail)
                        <div class="d-flex align-items-start mb-3">
                            <img
                                src="{{ $detail->item->photo_url }}"
                                class="me-3 rounded"
                                width="80" height="80"
                                style="object-fit: cover;"
                            >
                            <div>
                                <strong>{{ $detail->item->category->categori_name ?? 'Kategori Tidak Diketahui' }}</strong><br>
                                {{ $detail->item->nama_barang }}<br>
                                Jumlah: {{ $detail->qty_requested }} {{ $detail->item->satuan }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="col-12 col-md-4 col-lg-5 justify-content-md-center flex-column text-center text-md-center">
                    <small class="text-muted">Jadwal Pengambilan</small><br>
                    @if($pengajuan->tanggal_pengambilan)
                        <strong>{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengambilan)->format('d F Y') }}</strong>
                    @else
                        <strong class="text-danger">Belum Dijadwalkan</strong>
                    @endif
                </div>

                <div class="col-12 col-md-2 col-lg-2 d-flex flex-column align-items-center text-center text-md-end ms-md-auto">
                    <small class="text-muted d-block mb-1">Aksi</small>
                    <div class="d-flex justify-content-center justify-content-md-end gap-3">
                        <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $id }}">
                            <i class="bi bi-check-circle fs-5"></i>
                        </a>
                        <a href="#" class="text-primary" title="Detail">
                            <i class="bi bi-file-earmark-text fs-5"></i>
                        </a>
                        <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $id }}">
                            <i class="bi bi-x-circle fs-5"></i>
                        </a>

                    </div>
                </div>
            </div>

            {{-- Modals --}}
            @include('admin.modals.approve', ['pengajuan' => $pengajuan])
            @include('admin.modals.reject',  ['pengajuan' => $pengajuan])
        </div>
    @empty
        <div class="alert alert-info">Belum ada pengajuan dengan status "{{ $status }}".</div>
    @endforelse

    <a href="{{ route('admin.dashboard') }}" class="btn btn-success">Kembali ke Dashboard</a>
</div>

{{-- Alerts --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        {{ session('error') }}
        <button class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('rejected'))
    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
        {{ session('rejected') }}
        <button class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@endsection
