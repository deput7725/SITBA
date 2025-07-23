@extends('layouts.app')

@section('title', 'Selamat Datang - SITBA')

@push('styles')
<style>
    /* * BAGIAN LAYOUT UTAMA 
     */
    .welcome-container {
        display: flex; /* Menggunakan Flexbox */
        align-items: center; /* Posisikan item di tengah (vertikal) */
        justify-content: center; /* Posisikan item di tengah (horizontal) */
        width: 100%;
        min-height: 90vh; /* Tinggi minimal agar konten selalu di tengah layar */
        padding: 2rem;
    }

    .hero-section {
        width: 100%;
        max-width: 900px; /* Lebar maksimum konten */
        padding: 40px 50px;
        text-align: center;
        /* Gaya visual seperti background dan shadow tetap di sini */
        background: var(--container-bg, #ffffff);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    /* * LAYOUT UNTUK TOMBOL (BUTTON GRID)
     */
    .button-grid {
        display: grid; /* Menggunakan CSS Grid */
        gap: 25px; /* Jarak antar tombol */
        
        /* Default: 1 kolom untuk layar kecil (mobile) */
        grid-template-columns: 1fr; 
    }

    /* Tampilan untuk layar tablet dan lebih besar (lebar min 576px) */
    @media (min-width: 576px) {
        .button-grid {
            /* Ubah menjadi 2 kolom */
            grid-template-columns: repeat(2, 1fr); 
        }
    }

    /* * GAYA KARTU TOMBOL (Tidak mengubah layout, hanya visual)
     */
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
        transform: translateY(-5px); /* Efek mengangkat saat disentuh cursor */
    }

    .button-card .icon { font-size: 3rem; margin-bottom: 15px; }
    .button-card .label { font-size: 1.25rem; font-weight: 500; }
    .button-card p { font-size: 0.9rem; opacity: 0.9; margin: 5px 0 0 0; }
    .hero-section h1 { font-size: 36px; margin-bottom: 10px; color:var(--text-color) }
    .section-subtitle { font-size: 16px; color: var(--subtitle-color, #6c757d); margin-bottom: 30px; }
</style>
@endpush


@section('content')
{{-- Kontainer utama untuk memusatkan seluruh konten di tengah halaman --}}
<div class="welcome-container">
    <section class="hero-section">
        <h1>Sistem Informasi SITBA</h1>
        <p class="section-subtitle">Sistem Transaksi Informasi BAZNAS</p>

        {{-- Grid untuk menata tombol secara responsif --}}
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