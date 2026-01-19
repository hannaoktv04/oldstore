@extends('peri::layouts.auth')

@section('content')
<div class="auth-wrapper d-flex align-items-stretch justify-content-center vh-100 vw-100">
  <div class="auth-card d-flex flex-wrap flex-md-nowrap w-100 flex-row-reverse">

    <div class="image-section d-none d-md-flex align-items-center justify-content-center" style="width: 50%;">
      <img src="{{ asset('assets/img/old.jpg') }}" alt="Register Illustration"
           class="img-fluid" style="max-height: 500px; margin-right: 250px;">
    </div>

    <div class="form-container d-flex align-items-center justify-content-center" style="width: 100%; min-height: 100vh;">
      <div class="card w-100 p-4 p-md-5 rounded-4 shadow mx-3 mx-md-0" style="max-width: 500px;">

        <div class="text-start mb-1">
          <a href="{{ route('login') }}" class="text-decoration-none text-success small">← Login</a>
        </div>

        <h5 class="mb-3 text-secondary text-center">Register</h5>

        <form method="POST" action="{{ route('register') }}">
          @csrf

          <div class="row mb-2">
            <div class="col-md-6">
              <label for="nama" class="form-label">Nama</label>
              <input id="nama" type="text"
                     class="form-control @error('nama') is-invalid @enderror"
                     name="nama" value="{{ old('nama') }}" required>
              @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label for="username" class="form-label">Username</label>
              <input id="username" type="text"
                     class="form-control @error('username') is-invalid @enderror"
                     name="username" value="{{ old('username') }}" required>
              @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label for="no_telp" class="form-label">No. Telepon</label>
            <input id="no_telp" type="text"
                   class="form-control @error('no_telp') is-invalid @enderror"
                   name="no_telp" value="{{ old('no_telp') }}" required>
            @error('no_telp') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea id="alamat"
                      class="form-control @error('alamat') is-invalid @enderror"
                      name="alamat" rows="2" required>{{ old('alamat') }}</textarea>
            @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="row mb-4">
            <div class="col-md-6">
              <label for="password" class="form-label">Password</label>
              <input id="password" type="password"
                     class="form-control @error('password') is-invalid @enderror"
                     name="password" required>
              @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label for="password-confirm" class="form-label">Konfirmasi Password</label>
              <input id="password-confirm" type="password"
                     class="form-control"
                     name="password_confirmation" required>
            </div>
          </div>

          <button type="submit" class="btn btn-success w-100 rounded-pill">
            Register
          </button>

        </form>
      </div>
    </div>
  </div>
</div>
@endsection
