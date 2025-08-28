@extends('peri::layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Daftar Wishlist Pengguna</h4>
            <a href="{{ route('admin.dashboard.index') }}" class="btn btn-primary">
                <i class="me-1"></i> Kembali ke Dashboard
            </a>
        </div>
        
        <div class="card-body">
            @if ($wishlists->isEmpty())
                <div class="alert alert-info">Belum ada wishlist yang diajukan.</div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Pemohon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wishlists as $wishlist)
                            <tr>
                                <td>{{ $loop->iteration + ($wishlists->currentPage() - 1) * $wishlists->perPage() }}</td>
                                <td>
                                    {{ $wishlist->nama_barang }}
                                </td>
                                <td>{{ $wishlist->category->categori_name ?? '-' }}</td>
                                <td>{{ $wishlist->qty_diusulkan }}</td>
                                <td>{{ $wishlist->user->nama ?? 'Tidak diketahui' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $wishlists->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

