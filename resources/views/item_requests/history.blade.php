@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Status Pengajuan</h4>
    @forelse ($requests as $request)
        <div class="mb-5 p-3 border rounded">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Pengajuan #{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</h5>

                <div class="d-flex align-items-center gap-2">
                    <small>
                        @switch($request->status)
                            @case('draft') <span class="badge bg-secondary">Draft</span> @break
                            @case('submitted') <span class="badge bg-warning text-dark">Menunggu Persetujuan</span> @break
                            @case('approved') <span class="badge bg-primary">Disetujui</span> @break
                            @case('rejected') <span class="badge bg-danger">Ditolak</span> @break
                            @case('delivered') <span class="badge bg-info text-dark">Sedang Dikirim</span> @break
                            @case('received') <span class="badge bg-success">Barang Diterima</span> @break
                            @default <span class="badge bg-light text-dark">{{ ucfirst($request->status) }}</span>
                        @endswitch
                    </small>

                    <a href="#" class="btn btn-sm btn-outline-primary" title="Edit Pengajuan">
                        <i class="bi bi-pencil"></i>
                    </a>

                    @if(in_array($request->status, ['approved', 'delivered']))
                    <a href="{{ route('item_requests.set_pickup_date', $request->id) }}" class="btn btn-sm btn-outline-success" title="Atur Tanggal Pengambilan">
                        <i class="bi bi-calendar"></i>
                    </a>
                    @endif
                </div>
            </div>

            <p class="text-muted mb-3">{{ \Carbon\Carbon::parse($request->tanggal_permintaan)->format('d F Y') }}</p>

            @foreach ($request->details as $detail)
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
        <div class="alert alert-info">Belum ada pengajuan barang.</div>
    @endforelse
</div>
@endsection
