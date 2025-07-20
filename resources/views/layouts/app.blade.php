<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Menggunakan logo Anda sebagai favicon --}}
    <link rel="icon" href="{{ asset('images/logo_baznas.png') }}" type="image/png">
    
    {{-- Judul halaman akan dinamis, dengan judul default 'SITBA' --}}
    <title>@yield('title', 'SITBA')</title>

     {{-- Link CSS Bootstrap--}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- --- TAMBAHKAN BARIS DI BAWAH INI --- --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* --- Variabel Warna untuk Light & Dark Mode --- */
        :root {
            --bg-gradient: linear-gradient(to bottom right, #a7ca93, #b6ba89);
            --container-bg: #c4cfb7;
            --text-color: #2e4b2e;
            --button-bg: linear-gradient(to right, #6a8131, #8aa05e);
            --button-hover: linear-gradient(to right, #4a5c20, #728c39);
            --subtitle-color: #3a6030;
            --table-header-bg: #3d4937;
            --table-header-text: #ffffff;
            --table-stripe-bg: #f8f9fa;
        }

        body.dark {
            --bg-gradient: linear-gradient(to bottom right, #7e876b, #3f4f28);
            --container-bg: #3d4937;
            --text-color: #e3e3e3;
            --subtitle-color: #cdebb5;
            --table-header-bg: #2c3e50;
            --table-header-text: #e3e3e3;
            --table-stripe-bg: #4a5c2033;
        }

        /* --- Gaya Dasar --- */
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            background: var(--bg-gradient);
            transition: background 0.4s ease;
        }
        
        /* --- Logo & Tombol Mode --- */
        .logo-title {
            position: absolute; top: 20px; right: 30px;
        }
        .logo-title img {
            width: 98px; height: auto; filter: drop-shadow(0 1px 3px rgba(0, 0, 0, 0.2));
        }
        .toggle-mode {
            position: absolute; top: 20px; left: 30px; background: #ffffffaa;
            border: none; padding: 8px 14px; border-radius: 8px; font-size: 14px;
            cursor: pointer; transition: background 0.3s;
        }
        .toggle-mode:hover { background: #e5e5e5; }

        /* --- Slot untuk Konten Halaman --- */
        main {
            width: 100%;
        }
    </style>
    
    {{-- Slot untuk CSS tambahan jika halaman tertentu membutuhkannya --}}
    @stack('styles')
</head>
<body>
    <div class="logo-title">
        <img src="{{ asset('images/logo_baznas.png') }}" alt="Logo BAZNAS Provinsi Riau">     
    </div>
    <button class="toggle-mode" onclick="toggleMode()">☀ Mode</button>

    <main>
        {{-- Di sinilah konten dari halaman lain akan dimasukkan --}}
        @yield('content')
    </main>
    
    <script>
        function toggleMode() {
            document.body.classList.toggle("dark");
        }
    </script>
    
    {{-- Slot untuk JavaScript tambahan jika dibutuhkan --}}
    @stack('scripts')
</body>
</html>
