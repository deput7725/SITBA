@extends('layouts.app')
@section('title', 'Selamat Datang - SITBA')
@push('styles')
<style>
    /* Override body style agar konten tetap di tengah untuk halaman ini */
    body {
      padding: 0 150px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .hero-section {
        display: flex;
        flex-direction: column; 
        justify-content: center;
        align-items: center;  
        background: var(--container-bg);
        padding: 40px 60px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: background 0.3s ease;
    }
    .hero-section h1 { 
        color: var(--text-color); 
        font-size: 36px; 
        font-weight: 700; 
        margin-bottom: 10px; 
    }
    .section-subtitle { 
        text-decoration: none; 
        color: var(--subtitle-color); 
        font-weight: 600; 
        font-size: 16px; 
        transition: color 0.3s; 
        padding-bottom: 30px; 
    }
    .button-grid { 
        display: grid; 
        grid-template-columns: 1fr; 
        gap: 25px; 
        width: 100%; 
        max-width: 900px; 
    }
    @media (min-width: 576px) { 
        .button-grid { 
            grid-template-columns: repeat(2, 1fr); 
        } 
    }
    @media (min-width: 992px) {
        .button-grid { 
            grid-template-columns: repeat(3, 1fr); 
        } 
    }
    .button-card {
        background: var(--button-bg); 
        border-radius: 12px; 
        padding: 25px; 
        text-align: center; 
        text-decoration: none; 
        color: inherit; 
        box-shadow: 0 4px 10px rgba(91, 110, 43, 0.3); 
        transition: background 0.3s ease, transform 0.2s ease; 
    }
    .button-card:hover { 
        background: var(--button-hover); 
        transform: scale(1.03); 
    }
    .button-card .icon { 
        font-size: 3rem; 
        margin-bottom: 15px; 
        line-height: 1; 
    }
    .button-card .label { 
        font-size: 1.25rem; 
        font-weight: 500; 
        color: white; 
    }
    .button-card p { 
        font-size: 0.9rem; 
        color: white; 
        margin-top: 5px; 
        margin-bottom: 0; 
    }
</style>
@endpush
@section('content')
<div class="hero-section">
    <h1>Sistem Informasi SITBA</h1>
    <p class="section-subtitle">Sistem Transaksi Informasi BAZNAS</p>

    <div class="button-grid">
        <a href="{{ route('pendaftaran.perorangan') }}" class="button-card">
            <div class="icon">👤</div>
            <div class="label">Perorangan</div>
            <p>Lihat data pendaftar individu</p>
        </a>
        <a href="{{ route('pendaftaran.lembaga') }}" class="button-card">
            <div class="icon">🏢</div>
            <div class="label">Lembaga</div>
            <p>Lihat data pendaftar instansi</p>
        </a>
        <a href="#" class="button-card">
            <div class="icon">🗂️</div>
            <div class="label">Manajemen Data</div>
            <p>Unduh template & Unggah file</p>
        </a>
    </div>
</div>
@endsection
