@extends('layouts.app')

@section('title', 'Selamat Datang - SITBA')

@push('styles')
<style>
    /* * BAGIAN LAYOUT UTAMA */
    .welcome-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 90vh;
        padding: 2rem;
    }

    .hero-section {
        width: 100%;
        max-width: 900px;
        padding: 40px 50px;
        text-align: center;
        background: var(--container-bg, #ffffff);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        position: relative; /* Diperlukan untuk posisi absolut tombol logout */
    }

    .hero-section h1 {
        font-size: 36px;
        margin-bottom: 10px;
        color: var(--text-color);
    }

    .section-subtitle {
        font-size: 16px;
        color: var(--subtitle-color, #6c757d);
        margin-bottom: 40px; /* Beri jarak lebih ke tombol di bawahnya */
    }

    /* *
     * GAYA BARU UNTUK TOMBOL LOGOUT (PENEMPATAN DI POJOK KANAN ATAS)
     */
    .auth-actions {
        position: absolute;
        top: 20px;
        right: 20px;
    }

    .btn-auth {
        display: inline-block;
        padding: 8px 20px; /* Sedikit lebih kecil agar pas di pojok */
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
    }

    .btn-auth:hover {
        transform: translateY(-2px);
    }

    .btn-auth.logout {
        background-color: #c82333;
        color: white;
    }
    .btn-auth.logout:hover {
        background-color: #a21b29;
    }

    /* * LAYOUT DAN GAYA KARTU TOMBOL */
    .button-grid {
        display: grid;
        gap: 25px;
        grid-template-columns: 1fr;
    }

    @media (min-width: 576px) {
        .button-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .button-card {
        display: block;
        background: var(--button-bg, #007bff);
        border-radius: 12px;
        padding: 25px;
        text-decoration: none;
        color: white;
        transition: transform 0.2s ease;
    }

    .button-card:hover {
        transform: translateY(-5px);
    }

    .button-card .icon { font-size: 3rem; margin-bottom: 15px; }
    .button-card .label { font-size: 1.25rem; font-weight: 500; }
    .button-card p { font-size: 0.9rem; opacity: 0.9; margin: 5px 0 0 0; }
</style>
@endpush

@section('content')
<div class="welcome-container">
    <section class="hero-section">
        <div class="auth-actions">
            @auth
            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-auth logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
            @endauth
        </div>

        <h1>Sistem Informasi SITBA</h1>
        <p class="section-subtitle">Sistem Transaksi Informasi BAZNAS</p>

        <div class="button-grid">
            <a href="{{ route('pendaftaran.perorangan') }}" class="button-card">
                <div class="icon"><i class="bi bi-people-fill"></i></div>
                <div class="label">Perorangan</div>
                <p>Lihat data pendaftar individu</p>
            </a>
            <a href="{{ route('pendaftaran.lembaga') }}" class="button-card">
                <div class="icon"><i class="bi bi-buildings"></i></div>
                <div class="label">Lembaga</div>
                <p>Lihat data pendaftar instansi</p>
            </a>
        </div>

    </section>
</div>
@endsection

{{-- CATATAN: Kode JavaScript dari sebelumnya tidak diperlukan lagi jika kita menggunakan button type="submit" --}}
