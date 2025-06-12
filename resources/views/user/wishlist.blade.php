@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Wishlist Saya</h4>

    @forelse ($wishlists as $item)
        <div class="card mb-3 p-3 shadow-sm">
            <div class="d-flex justify-content-between">
                <div>
                    <h5 class="mb-1">{{ $item->nama_barang }}</h5>
                    <small class="text-muted">{{ $item->category->nama ?? 'Kategori tidak diketahui' }}</small>
                    <p class="mb-1">{{ $item->deskripsi }}</p>
                    <span class="badge bg-secondary">Jumlah Diusulkan: {{ $item->qty_diusulkan }}</span>
                </div>
                <div class="text-end">
                    <span class="badge 
                        {{ $item->status == 'pending' ? 'bg-warning' : ($item->status == 'diakomodasi' ? 'bg-success' : 'bg-danger') }}">
                        {{ ucfirst($item->status) }}
                    </span>
                    @if($item->catatan_admin)
                        <p class="text-muted mt-2"><strong>Catatan:</strong> {{ $item->catatan_admin }}</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Belum ada wishlist yang diajukan.</div>
    @endforelse
</div>
@endsection
