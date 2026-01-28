@extends('peri::layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-0">Manajemen Anggota</h4>
            <p class="text-muted small mb-4">Kelola informasi profil, kontak, role, dan alamat anggota secara terpusat.</p>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-pill px-4 mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 py-3 text-muted small fw-bold text-center">NO</th>
                            <th class="py-3 text-muted small fw-bold">NAMA & USERNAME</th>
                            <th class="py-3 text-muted small fw-bold">KONTAK (EMAIL/TELP)</th>
                            <th class="py-3 text-muted small fw-bold">ROLE</th>
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
                                <div class="small"><i class="bi bi-envelope me-1"></i> {{ $user->email }}</div>
                                <div class="small text-muted"><i class="bi bi-whatsapp me-1"></i> {{ $user->no_telp ?? '-' }}</div>
                            </td>
                            {{-- Kolom Role Baru --}}
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge bg-primary rounded-pill small" style="font-size: 10px;">ADMIN</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill small" style="font-size: 10px;">USER</span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-truncate" style="max-width: 250px;">
                                    {{ $user->alamat ?? 'Alamat belum diatur' }}
                                </div>
                            </td>
                            <td class="pe-3 text-end">
                                <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold shadow-sm border" 
                                        data-bs-toggle="modal" data-bs-target="#editUser{{ $user->id }}">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
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

{{-- MODAL EDIT --}}
@foreach ($users as $user)
<div class="modal fade" id="editUser{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0">Ubah Data Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold">Nomor Telepon (WhatsApp)</label>
                            <input type="text" name="no_telp" class="form-control" value="{{ $user->no_telp }}" placeholder="08123456789">
                        </div>

                        {{-- Input Role Baru --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold">Role / Hak Akses</label>
                            <select name="role" class="form-select" {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User (Anggota Biasa)</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin (Pengelola)</option>
                            </select>
                            @if(auth()->id() == $user->id)
                                <input type="hidden" name="role" value="{{ $user->role }}">
                                <small class="text-muted d-block mt-1">Anda tidak bisa mengubah role Anda sendiri.</small>
                            @endif
                        </div>

                        <div class="col-12 mb-0">
                            <label class="form-label small fw-bold">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap...">{{ $user->alamat }}</textarea>
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
    .table tbody td { border-bottom: 1px solid #f8f9fa; padding-top: 15px; padding-bottom: 15px; }
    .form-control, .form-select { border-radius: 0.75rem; padding: 0.6rem 1rem; border: 1px solid #e9ecef; }
    .form-control:focus, .form-select:focus { box-shadow: none; border-color: #0d6efd; }
</style>
@endsection