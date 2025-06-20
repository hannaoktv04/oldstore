@extends('layouts.auth')

@section('content')
<style>
    body {
        background-image: url('{{ asset('assets/img/background.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
    }

    .register-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        backdrop-filter: blur(5px);
    }

    .register-box {
        background-color: rgba(1, 69, 49, 0.644);
        color: white;
        border-radius: 20px;
        max-width: 800px;
        width: 100%;
        padding: 2rem;
    }

    .form-control,
    .form-select {
        background-color: rgb(245, 245, 245);
        border: 1px solid #000000;
        color: rgb(0, 0, 0);
    }

    .form-control::placeholder {
        color: #3b3b3b;
    }

    .btn-register {
        background: linear-gradient(90deg, #2d7d49, #149a31);
        border: none;
    }

    @media (min-width: 768px) {
        .register-wrapper {
            padding: 5rem;
        }
    }
</style>

<div class="register-wrapper">
    <div class="register-box shadow">
        <div class="text-start mb-3">
            <a href="{{ route('login') }}" class="text-white text-decoration-none"><small>← Login</small></a>
        </div>

        <h1 class="text-center mb-3">Register</h1>
        <p class="text-center text-white-50 mb-4">Daftar akun baru untuk mengakses sistem</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nama" class="form-label">Nama</label>
                    <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama') }}" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="username" class="form-label">Username</label>
                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required>
                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="jabatan" class="form-label">Jabatan</label>
                    <input id="jabatan" type="text" class="form-control @error('jabatan') is-invalid @enderror" name="jabatan" value="{{ old('jabatan') }}" required>
                    @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label">Role</label>
                    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">Pilih Role</option>
                        <option value="pegawai" {{ old('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nip" class="form-label">NIP</label>
                    <input id="nip" type="text" class="form-control @error('nip') is-invalid @enderror" name="nip" value="{{ old('nip') }}" required>
                    @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="password-confirm" class="form-label">Konfirmasi Password</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                </div>
            </div>

            <button type="submit" class="btn btn-register w-100 rounded-pill text-white">Register</button>
        </form>
    </div>
</div>
@endsection
