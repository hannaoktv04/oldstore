@extends('peri::layouts.auth')

@section('content')
<div class="card shadow p-4" style="width: 360px;">
  <h3 class="mb-4 text-center">Ubah Password Baru</h3>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-3">
      <label for="email" class="form-label">Email address</label>
      <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autofocus>
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password Baru</label>
      <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
      @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
      <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Ubah Password</button>
  </form>
</div>
@endsection
