@extends('layouts.auth')

@section('content')
<div class="container position-relative d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  
  <img src="{{ asset('assets/img/hello.png') }}" alt="Maskot" class="mascot d-none d-md-block">

  <div class="login-box bg-white p-4 p-md-5 rounded-4 shadow w-100">
    <div class="text-end mb-2">
      <a href="{{ route('register') }}" class="text-decoration-none text-dark"><small>Register →</small></a>
    </div>

    <h1 class="text-center fw-bold brand-custom mb-2" style="font-size: 3.5rem;">PERI</h1>
    <p class="text-center text-muted mb-4"><small>
      Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ac tortor volutpat, vulputate massa non.
    </small></p>

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-3">
        <label for="nip" class="form-label">NIP</label>
        <input type="text" 
               class="form-control @error('nip') is-invalid @enderror" 
               id="nip" 
               name="nip" 
               placeholder="Masukkan NIP" 
               required 
               autofocus
               value="{{ old('nip') }}">
        @error('nip')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               id="password" 
               name="password" 
               placeholder="**************" 
               required>
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <a href="{{ route('password.request') }}" class="text-decoration-none small">Forgot Password?</a>
      </div>

      <button type="submit" class="btn btn-primary w-100 rounded-pill">Login</button>
    </form>
  </div>
</div>
@endsection