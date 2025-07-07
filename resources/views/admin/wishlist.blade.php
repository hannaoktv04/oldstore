@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Daftar Wishlist Pengguna</h4>

    @foreach ($wishlists as $wishlist)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $wishlist->nama_barang }}</h5>
                <p class="mb-1">{{ $wishlist->deskripsi }}</p>
                <p class="mb-1">Kategori: {{ $wishlist->category->categori_name ?? '-' }}</p>
                <p class="mb-1">Jumlah Diusulkan: {{ $wishlist->qty_diusulkan }}</p>
                <p class="mb-1">Pemohon: <strong>{{ $wishlist->user->nama ?? 'Tidak diketahui' }}</strong></p>
                <p class="mb-1">Status:
                    @if($wishlist->status == 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($wishlist->status == 'diakomodasi')
                        <span class="badge bg-success">Diakomodasi</span>
                    @else
                        <span class="badge bg-danger">Ditolak</span>
                    @endif
                </p>
                @if($wishlist->status == 'pending')
                    <form action="{{ route('admin.wishlist.akomodasi', $wishlist->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-success btn-sm">Akomodasi</button>
                    </form>
                    <form action="{{ route('admin.wishlist.tolak', $wishlist->id) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="catatan_admin" value="Permintaan tidak relevan.">
                        <button class="btn btn-danger btn-sm">Tolak</button>
                    </form>
                @else
                    <p class="mt-2"><strong>Catatan Admin:</strong> {{ $wishlist->catatan_admin }}</p>
                @endif
            </div>
        </div>
    @endforeach

    @if ($wishlists->isEmpty())
        <div class="alert alert-info">Belum ada wishlist yang diajukan.</div>
    @endif

    <a href="{{ route('admin.dashboard') }}" class="btn btn-success mt-3">Kembali ke Dashboard</a>
</div>
@endsection
