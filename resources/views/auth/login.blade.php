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

    .login-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .login-box {
        background-color: rgba(1, 69, 49, 0.644);
        color: white;
        border-radius: 20px;
        max-width: 420px;
        width: 100%;
        padding: 2rem;
        backdrop-filter: blur(5px);
    }

    .login-box h1 {
        font-size: 2.5rem;
        font-weight: 700;
    }

    .btn-login {
        background: linear-gradient(90deg, #2d7d49, #149a31);
        border: none;
    }

    .form-control {
        background-color: #ffffff;
        border: 1px solid #333;
        color: white;
    }

    .form-control::placeholder {
        color: #aaa;
    }

    @media (min-width: 768px) {
        .login-wrapper {
            padding: 5rem;
        }
    }
</style>

<div class="login-wrapper">
    <div class="login-box shadow">
        <div class="text-end mb-3">
            <a href="{{ route('register') }}" class="text-white text-decoration-none"><small>Register →</small></a>
        </div>

        <h1 class="text-center mb-3">LOGIN</h1>
        <p class="text-center text-white mb-4">login with username & password</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text"
                       class="form-control @error('username') is-invalid @enderror"
                       id="username"
                       name="username"
                       placeholder="Your username"
                       required autofocus
                       value="{{ old('username') }}">
                @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       id="password"
                       name="password"
                       placeholder="********"
                       required>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" class="text-decoration-none text-white-50 small">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-login w-100 rounded-pill text-white">Login</button>
        </form>
    </div>
</div>
@endsection
