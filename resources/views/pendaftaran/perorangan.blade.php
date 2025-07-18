<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pendaftar Perorangan - SITBA</title>
    <!-- Kita akan menggunakan Bootstrap untuk styling yang cepat dan rapi -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Data Pendaftar Zakat (Perorangan)</h1>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Pekerjaan</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Lakukan perulangan untuk setiap data pendaftar yang dikirim dari Controller --}}
                    @forelse ($pendaftar as $index => $p)
                        <tr>
                            {{-- $pendaftar->firstItem() digunakan untuk penomoran paginasi --}}
                            <td>{{ $pendaftar->firstItem() + $index }}</td>
                            <td>{{ $p->nama }}</td>
                            <td>{{ $p->nik }}</td>
                            <td>{{ $p->pekerjaan ?? '-' }}</td>
                            <td>{{ $p->email ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tampilkan link untuk paginasi --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $pendaftar->links() }}
        </div>
    </div>
</body>
</html>
