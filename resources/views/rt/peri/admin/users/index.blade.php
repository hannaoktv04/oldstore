@extends('peri::layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-0">Manajemen Anggota</h4>
            <p class="text-muted small mb-4">Kelola informasi profil, kontak, dan alamat anggota secara terpusat.</p>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 py-3 text-muted small fw-bold text-center">NO</th>
                            <th class="py-3 text-muted small fw-bold">NAMA & USERNAME</th>
                            <th class="py-3 text-muted small fw-bold">KONTAK (EMAIL/TELP)</th>
                            <th class="py-3 text-muted small fw-bold">ALAMAT PENGIRIMAN</th>
                            <th class="pe-3 py-3 text-muted small fw-bold text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $index => $user)
                        <tr>
                            <td class="ps-3 text-center small">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $user->nama }}</div>
                                <div class="text-muted small">@ {{ $user->username }}</div>
                            </td>
                            <td>
                                <div class="small"><i class="ri-mail-line me-1"></i> {{ $user->email }}</div>
                                <div class="small text-muted"><i class="ri-phone-line me-1"></i> {{ $user->no_telp ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="small text-truncate" style="max-width: 250px;" title="{{ $user->alamat }}">
                                    {{ $user->alamat ?? '-' }}
                                </div>
                            </td>
                            <td class="pe-3 text-end">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-4" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal{{ $user->id }}">
                                    <i class="ri-edit-line me-1"></i> Edit Profil
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT USER --}}
@foreach ($users as $user)
<div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="fw-bold">Update Profil Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold">Nomor Telepon (WhatsApp)</label>
                            <input type="text" name="no_telp" class="form-control" value="{{ $user->no_telp }}" placeholder="Contoh: 08123456789">
                        </div>
                        <div class="col-12 mb-0">
                            <label class="form-label small fw-bold">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="4" placeholder="Masukkan alamat lengkap untuk keperluan pengiriman barang...">{{ $user->alamat }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .table thead th { letter-spacing: 0.05em; border-bottom: none; font-size: 11px; }
    .table tbody td { border-bottom: 1px solid #f8f9fa; }
</style>
@endsection