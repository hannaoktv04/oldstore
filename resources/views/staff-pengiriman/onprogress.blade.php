@extends('layouts.staff')

@section('content')
<div class="container">

    <h5 class="mb-4">Daftar Pengiriman On Progress</h5>

    <ul class="list-group">
        @forelse($pengiriman as $item)
            <li class="list-group-item rounded-3 mb-3 shadow-sm">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong>{{ $item->request->user->nama }}</strong>
                        <small class="text-muted">| Resi: KP{{ str_pad($item->request->id, 4, '0', STR_PAD_LEFT) }}</small>
                    </div>
                    <span class="badge bg-warning text-dark">On Progress</span>
                </div>

                <div class="d-flex flex-column gap-2">

                    @foreach($item->request->details as $detail)
                        <a href="{{ route('staff-pengiriman.konfirmasi', ['kodeResi' => 'KP' . str_pad($item->request->id, 4, '0', STR_PAD_LEFT)]) }}" 
                           class="text-decoration-none text-dark hover-shadow p-2 rounded d-flex align-items-center">
                            <img src="{{ $detail->item->gallery->first() 
                                ? asset('storage/' . $detail->item->gallery->first()) 
                                : asset('assets/img/default.png') 
                            }}" alt="{{ $detail->item->nama_barang }}" width="50" height="50" class="rounded border me-3" style="object-fit: cover;">

                            <div>
                                <div class="fw-semibold">{{ $detail->item->nama_barang }}</div>
                                <small class="text-muted">Jumlah: {{ $detail->qty_requested }} {{ $detail->item->satuan }}</small>
                            </div>
                        </a>
                    @endforeach

                </div>

            </li>
        @empty
            <li class="list-group-item text-muted text-center">Belum ada pengiriman on progress.</li>
        @endforelse
    </ul>

</div>

<style>
    .hover-shadow:hover {
        background-color: #f8f9fa;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: 0.2s ease;
    }
</style>
@endsection
