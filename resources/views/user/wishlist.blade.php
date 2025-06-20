@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Daftar Wishlist</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($wishlists->count())
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah Diusulkan</th>
                        <th>Status</th>
                        <th>Catatan Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wishlists as $item)
                    <tr>
                        <td><img src="{{ asset('storage/' . ($item->photo->image ?? 'placeholder.jpg')) }}" width="80" height="80"
                        class="rounded me-3" style="object-fit: cover;">{{ $item->nama_barang }}</td>
                        <td>{{ $item->category->categori_name ?? '-' }}</td>
                        <td class="text-center">{{ $item->qty_diusulkan }}</td>
                        <td>
                            <span class="badge 
                                @if($item->status == 'approved') bg-success 
                                @elseif($item->status == 'rejected') bg-danger 
                                @else bg-warning text-dark 
                                @endif">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td>{{ $item->catatan_admin ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>Belum ada wishlist.</p>
    @endif
</div>
@endsection
