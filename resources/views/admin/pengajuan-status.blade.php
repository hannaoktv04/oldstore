@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Daftar Pengajuan - Status: {{ ucfirst($status) }}</h4>

    @forelse ($pengajuans as $pengajuan)
        <div class="mb-5 p-3 border rounded">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0">Pengajuan #{{ str_pad($pengajuan->id, 3, '0', STR_PAD_LEFT) }}</h5>
                    <small class="text-muted">
                        Pemohon: <strong>{{ $pengajuan->user->name ?? 'Tidak diketahui' }}</strong> &middot;
                        Diajukan: {{ \Carbon\Carbon::parse($pengajuan->tanggal_permintaan)->format('d F Y') }}
                    </small>
                </div>

                <div class="text-end">
                    <small class="text-muted">Jadwal Pengambilan</small><br>
                    <strong>{{ \Carbon\Carbon::parse($pengajuan->pickup_schedule)->format('d F Y') }}</strong>
                </div>

                <div class="text-end">
                    <small class="text-muted d-block mb-1">Aksi</small>
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="#" class="text-success" title="Setujui"><i class="bi bi-check-circle fs-5"></i></a>
                        <a href="#" class="text-primary" title="Detail"><i class="bi bi-file-earmark-text fs-5"></i></a>
                        <a href="#" class="text-danger" title="Tolak"><i class="bi bi-x-circle fs-5"></i></a>
                    </div>
                </div>
            </div>

            @foreach ($pengajuan->details as $detail)
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('assets/img/products/' . $detail->item->image) }}" class="me-3 rounded" width="80" height="80" style="object-fit: cover;">
                    <div>
                        <strong>{{ $detail->item->kategori ?? 'Kategori Tidak Diketahui' }}</strong><br>
                        {{ $detail->item->nama_barang }} <br>
                        Jumlah: {{ $detail->qty_requested }} {{ $detail->item->satuan }}
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <div class="alert alert-info">Belum ada pengajuan dengan status "{{ $status }}".</div>
    @endforelse

    <a href="{{ route('admin.dashboard') }}" class="btn btn-success">Kembali ke Dashboard</a>
</div>
@endsection
