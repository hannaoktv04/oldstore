@extends('layouts.staff')

@section('content')
<div class="container">

    <h5 class="mb-4">Daftar Pengiriman Selesai</h5>

    <form method="GET" class="mb-4 d-flex align-items-center gap-2">
        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control" style="max-width: 200px;">
        <button type="submit" class="btn btn-success btn-sm">Filter</button>
        <a href="{{ route('staff-pengiriman.selesai') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </form>

    @forelse($pengiriman as $item)
        <div class="card mb-3 shadow-sm rounded-4">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong>{{ $item->request->user->nama }}</strong>
                        <small class="text-muted">| Resi: KP{{ str_pad($item->request->id, 4, '0', STR_PAD_LEFT) }}</small> 
                    </div>
                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#buktiModal{{ $item->id }}">
                        <i class="bi bi-image"></i> Lihat Bukti
                    </button>
                </div>

                <div class="mb-3">
                    <small class="text-muted">
                        Diajukan: {{ \Carbon\Carbon::parse($item->request->tanggal_permintaan)->format('d M Y H:i') }} |
                        Dikirim: {{ \Carbon\Carbon::parse($item->tanggal_kirim)->format('d M Y H:i') }}
                    </small>
                </div>

                <div class="list-group list-group-flush">
                    @foreach($item->request->details as $detail)
                        <div class="list-group-item d-flex align-items-center py-2">
                            <img src="{{ $detail->item->gallery->first() 
                                ? asset('storage/' . $detail->item->gallery->first()) 
                                : asset('assets/img/default.png') 
                            }}" alt="{{ $detail->item->nama_barang }}" width="50" height="50" class="rounded border me-3" style="object-fit: cover;">

                            <div>
                                <div class="fw-semibold">{{ $detail->item->nama_barang }}</div>
                                <small class="text-muted">Jumlah: {{ $detail->qty_requested }} {{ $detail->item->satuan }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

        <div class="modal fade" id="buktiModal{{ $item->id }}" tabindex="-1" aria-labelledby="buktiLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="buktiLabel{{ $item->id }}">Bukti Pengiriman</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $item->bukti_foto) }}" alt="Bukti Pengiriman" class="img-fluid rounded shadow-sm">
                        @if($item->catatan)
                            <small class="d-block mt-2 text-muted">Catatan: {{ $item->catatan }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @empty
        <div class="alert alert-info">Belum ada pengiriman selesai.</div>
    @endforelse

</div>
@endsection
