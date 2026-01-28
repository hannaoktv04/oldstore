@extends('peri::layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-gear me-2"></i>Pengaturan Profil</h5>
                </div>
                <div class="card-body p-4">
                    {{-- Alert Sukses --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-pill px-4" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username</label>
                                <input type="text" name="username" class="form-control rounded-pill bg-light" 
                                       value="{{ old('username', auth()->user()->username) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control rounded-pill" 
                                       value="{{ old('nama', auth()->user()->nama) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control rounded-pill" 
                                       value="{{ old('email', auth()->user()->email) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nomor Telepon</label>
                                <input type="text" name="no_telp" class="form-control rounded-pill" 
                                       value="{{ old('no_telp', auth()->user()->no_telp) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat</label>
                                <input type="text" name="alamat" class="form-control rounded-pill" 
                                       value="{{ old('alamat', auth()->user()->alamat) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" name="password" class="form-control rounded-pill" placeholder="********">
                                <small class="text-muted ms-3">Gunakan minimal 8 karakter dengan kombinasi huruf dan angka.</small>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ url('/') }}" class="btn btn-light rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection