@extends('layouts.staff')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Daftar Pengiriman Waiting</h4>

    @forelse($pengiriman as $item)
        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $item->request->user->nama }}</strong> <br>
                    <small class="text-muted">Resi: KP{{ str_pad($item->request->id, 6, '0', STR_PAD_LEFT) }}</small><br>
                    <small class="text-muted">Belum di-scan / Belum dikirim</small>
                </div>
                <a href="{{ route('staff-pengiriman.konfirmasi', 'KP' . str_pad($item->request->id, 6, '0', STR_PAD_LEFT)) }}" 
                   class="btn btn-outline-primary btn-sm">
                    Scan & Mulai Kirim
                </a>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">
            Belum ada pengiriman waiting.
        </div>
    @endforelse
</div>
@endsection
