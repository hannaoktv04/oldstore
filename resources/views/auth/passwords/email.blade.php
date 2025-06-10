@extends('layouts.auth')

@section('content')
<div class="card shadow p-4" style="width: 360px;">
  <h3 class="mb-4 text-center">Reset Password</h3>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="mb-3">
      <label for="email" class="form-label">Email address</label>
      <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
    <button type="submit" class="btn btn-primary w-100">Kirim Link Reset Password</button>
  </form>
</div>
@endsection
