@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Daftar Wishlist</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($wishlists->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Jumlah Diusulkan</th>
                    <th>Status</th>
                    <th>Catatan Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wishlists as $item)
                <tr>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->category->categori_name ?? '-' }}</td>
                    <td>{{ $item->qty_diusulkan }}</td>
                    <td><span class="badge bg-warning text-dark">{{ ucfirst($item->status) }}</span></td>
                    <td>{{ $item->catatan_admin ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Belum ada wishlist.</p>
    @endif
</div>
@endsection
