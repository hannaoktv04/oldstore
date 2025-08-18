@extends('peri::layouts.auth')

@section('content')
<div class="auth-wrapper d-flex align-items-stretch justify-content-center vh-100 vw-100">
  <div class="auth-card d-flex flex-wrap flex-md-nowrap w-100">

    <div class="image-section d-none d-md-flex align-items-center justify-content-center" style="width: 60%;">
      <img src="{{ asset('assets/img/login.png') }}" alt="Login Illustration" class="img-fluid" style="max-height: 450px;">
    </div>

    <div class="form-container d-flex align-items-center justify-content-center w-100" style="min-height: 100vh;">
      <div class="card w-100 p-4 p-md-5 rounded-4 shadow mx-3 mx-md-0" style="max-width: 420px;">
        <div class="text-end">
          <a href="{{ route('register') }}" class="text-decoration-none text-success small">Register →</a>
        </div>

        <div class="text-center mb-1">
          <img src="{{ asset('assets/img/peri.png') }}" alt="Logo" style="height: 70px;">
        </div>

        <form method="POST" action="{{ route('login') }}">
          @csrf
          <h4 class="mb-5 text-secondary text-center">Login</h4>

          <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
          <input type="password" name="password" class="form-control mb-5" placeholder="Password" required>

          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="remember">
              <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <a href="{{ route('password.request') }}" class="small text-success">Forget Password?</a>
          </div>

          <button type="submit" class="btn btn-success w-100 rounded-pill">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
