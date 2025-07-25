@extends('layouts.app')

@section('title', 'Login - SITBA')

@push('styles')
<style>
    /* *
     * GAYA UTAMA (Diadaptasi dari kode Anda)
     */
    .login-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 90vh;
        padding: 2rem;
    }

    .login-box {
        width: 100%;
        max-width: 450px; /* Lebar ideal untuk form login */
        padding: 40px 35px;
        text-align: center;
        background: var(--container-bg, #ffffff);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: background 0.4s ease;
    }

    .login-box h1 {
        font-size: 28px;
        margin-bottom: 10px;
        color: var(--text-color);
        font-weight: 600;
    }

    .login-box .section-subtitle {
        font-size: 16px;
        color: var(--subtitle-color, #6c757d);
        margin-bottom: 35px;
    }

    /* *
     * GAYA FORM INPUT
     */
    .form-group {
        margin-bottom: 20px;
        text-align: left;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-color);
        font-size: 14px;
    }

    .input-icon-wrapper {
        position: relative;
    }

    .input-icon-wrapper .icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--subtitle-color);
        font-size: 18px;
    }

    .form-control-login {
        width: 100%;
        padding: 12px 15px 12px 50px; /* Padding kiri untuk ikon */
        border: 1px solid #ced4da;
        border-radius: 8px;
        background-color: #f8f9fa;
        color: #495057;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-size: 16px;
    }

    .form-control-login:focus {
        outline: none;
        border-color: #8aa05e;
        box-shadow: 0 0 0 3px rgba(138, 160, 94, 0.25);
    }

    /* *
     * GAYA TOMBOL LOGIN
     */
    .btn-login {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 8px;
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        background: var(--button-bg, #007bff);
        transition: background 0.3s ease;
        margin-top: 10px;
    }

    .btn-login:hover {
        background: var(--button-hover, #0056b3);
    }
    
    /* *
     * GAYA UNTUK PESAN ERROR
     */
     .error-message {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 5px;
        display: block;
     }

</style>
@endpush


@section('content')
<div class="login-container">
    <div class="login-box">
        <h1>Login Akun</h1>
        <p class="section-subtitle">Silakan masuk untuk melanjutkan ke dasbor</p>

        {{-- Menampilkan error login umum --}}
        @if(session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Input untuk Username --}}
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-icon-wrapper">
                    <i class="bi bi-person icon"></i>
                    <input type="text" id="username" name="username" class="form-control-login" placeholder="Masukkan username Anda" value="{{ old('username') }}" required autofocus>
                </div>
                @error('username')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            {{-- Input untuk Password --}}
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon-wrapper">
                    <i class="bi bi-lock icon"></i>
                    <input type="password" id="password" name="password" class="form-control-login" placeholder="Masukkan password Anda" required>
                </div>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            {{-- Tombol Submit --}}
            <button type="submit" class="btn-login">
                Login
            </button>
        </form>
    </div>
</div>
@endsection
