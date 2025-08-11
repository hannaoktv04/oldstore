@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Daftar Wishlist Pengguna</h4>
            <a href="{{ route('admin.dashboard.index') }}" class="btn btn-success btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
        
        <div class="card-body">
            @if ($wishlists->isEmpty())
                <div class="alert alert-info">Belum ada wishlist yang diajukan.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Barang</th>
                                <th>Deskripsi</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Pemohon</th>
                                <th>Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wishlists as $wishlist)
                            <tr>
                                <td>{{ $loop->iteration + ($wishlists->currentPage() - 1) * $wishlists->perPage() }}</td>
                                <td>
                                    <strong>{{ $wishlist->nama_barang }}</strong>
                                </td>
                                <td>{{ Str::limit($wishlist->deskripsi, 50) }}</td>
                                <td>{{ $wishlist->category->categori_name ?? '-' }}</td>
                                <td>{{ $wishlist->qty_diusulkan }}</td>
                                <td>{{ $wishlist->user->nama ?? 'Tidak diketahui' }}</td>
                                <td>
                                    @if($wishlist->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($wishlist->status == 'diakomodasi')
                                        <span class="badge bg-success">Diakomodasi</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($wishlist->status == 'pending')
                                        <div class="btn-group btn-group-sm" role="group">
                                            <form action="{{ route('admin.wishlist.akomodasi', $wishlist->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="Akomodasi">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $wishlist->id }}" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="modal fade" id="rejectModal{{ $wishlist->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.wishlist.tolak', $wishlist->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Tolak Wishlist</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="catatan_admin" class="form-label">Alasan Penolakan</label>
                                                                <textarea class="form-control" id="catatan_admin" name="catatan_admin" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Konfirmasi Tolak</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <small class="text-muted">{{ $wishlist->catatan_admin }}</small>
                                    @endif
                                </td>
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

@push('styles')
<style>
    .table th {
        white-space: nowrap;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.765625rem;
    }
</style>
@endpush