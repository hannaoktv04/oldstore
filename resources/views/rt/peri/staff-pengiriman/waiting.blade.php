@extends('peri::layouts.staff')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 d-flex align-items-center">
        <i class="ri-box-3-line me-2 text-success"></i>
        Daftar Pengiriman Waiting
    </h4>

    @forelse($pengiriman as $item)
        @php
            $filteredDetails = $item->details->filter(fn($detail) => $detail->qty_approved > 0);
        @endphp

        @if($filteredDetails->isNotEmpty())
        <div class="card mb-4 shadow-sm border-0 rounded-4">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap">
                    <div>
                        <h6 class="mb-1">{{ $item->user->nama }}</h6>
                        <small class="text-muted">
                            Resi: <span class="fw-semibold">KP{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </small><br>
                    </div>

                    <form method="POST" action="{{ route('staff-pengiriman.assign', $item->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm mt-2 mt-md-0">
                            <i class="ri-box-3-line me-1"></i> Assign to Me
                        </button>
                    </form>
                </div>

                <div class="items-list">
                    @foreach($filteredDetails as $detail)
                        <div class="item-row d-flex align-items-start py-2">
                            <img
                                src="{{ $detail->item->gallery->first()
                                    ? asset('storage/' . $detail->item->gallery->first())
                                    : asset('assets/img/default.png') }}"
                                alt="{{ $detail->item->nama_barang }}"
                                class="rounded-img me-3"
                                width="60" height="60">

                            <div>
                                <div class="fw-semibold">{{ $detail->item->nama_barang }}</div>
                                <small class="text-muted">
                                    Jumlah: {{ $detail->qty_approved }} {{ $detail->item->satuan->nama_satuan }}
                                </small><br>
                                @if($detail->catatan)
                                    <small class="text-muted">Catatan: {{ $detail->catatan }}</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
        @endif
    @empty
        <div class="alert alert-info text-center">
            Belum ada pengiriman waiting.
        </div>
    @endforelse
</div>

<style>
  .card:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,0.05);
    transition: 0.3s ease;
  }
  .fw-semibold { font-weight: 600; }

  .items-list .item-row + .item-row {
    border-top: 1px solid #eee;
  }

  .rounded-img {
    object-fit: cover;
    border-radius: .5rem;
    border: 1px solid rgba(0,0,0,.08);
  }
</style>
@endsection
