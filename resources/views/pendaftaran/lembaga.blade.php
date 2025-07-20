@extends('layouts.app')

@section('title', 'Data Pendaftar Lembaga - SITBA')

@push('styles')
<style>
    /* ... (CSS dari tema Anda yang sudah ada tidak perlu diubah) ... */
    .content-page { 
        width: 100%; 
        max-width: 1200px; 
        margin: 120px auto 40px auto; 
        background: var(--container-bg); 
        padding: 30px; 
        border-radius: 12px; 
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); 
        color: var(--text-color); 
    }
    .page-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 25px; 
        border-bottom: 1px solid var(--subtitle-color); 
        padding-bottom: 15px; 
    }
    .page-header h1 { 
        color: var(--text-color); 
        font-size: 2rem; 
        font-weight: 600; 
        margin: 0; 
    }
    .btn-back { 
        color: var(--subtitle-color); 
        font-size: 2.5rem; 
        text-decoration: none; 
        transition: color 0.3s ease, 
        transform 0.2s ease; 
    }
    .btn-back:hover { 
        color: var(--text-color); 
        transform: scale(1.1); 
    }
    .table thead th { 
        background-color: var(--subtitle-color); 
        color: #ffffff; 
        vertical-align: middle;
    }
    
    .action-box {
        background-color: rgba(0,0,0,0.03);
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }
    .action-box h3 {
        font-size: 1.25rem;
        font-weight: 500;
        margin-top: 0;
        margin-bottom: 15px;
        color: var(--text-color);
    }
    .alert { 
        white-space: pre-wrap; 
    }

    /* --- CSS BARU UNTUK KOLOM CHECKBOX --- */
    .table th.checkbox-col, .table td.checkbox-col { 
        width: 1%; 
        text-align: center; 
        vertical-align: middle;
    }
    .btn-action {
        background: var(--button-bg);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        margin-top: 10px; /* Jarak antar tombol */
    }
    .btn-action:hover {
        background: var(--button-hover);
        color: white;
    }
    .btn-success{
        background: var(--button-bg);
        border: none;
    }
    .btn-primary{
        background: var(--button-bg);
        border: none;
    }
</style>
@endpush

@section('content')
<div class="content-page">
    <div class="page-header">
        <h1>Data Pendaftar Zakat (Lembaga)</h1>
        <a href="{{ route('landing') }}" class="btn-back" title="Kembali ke Menu Utama">
            <i class="bi bi-arrow-left-square-fill"></i>
        </a>
    </div>

    <div class="action-box">
        <h3>Filter & Aksi Cepat</h3>
        
        <div class="row g-3 mb-4 align-items-end">
             <div class="col-md-4">
                <label for="filter_lembaga" class="form-label fw-bold">Pilih Lembaga</label>
                <form action="{{ route('pendaftaran.lembaga') }}" method="GET" id="form-filter">
                    <select class="form-select" id="filter_lembaga" name="id_lembaga" onchange="document.getElementById('form-filter').submit();">
                        <option value="">-- Tampilkan Semua Lembaga --</option>
                        @foreach ($daftarLembaga as $lembaga)
                            <option value="{{ $lembaga->id_lb }}" {{ $selectedLembagaId == $lembaga->id_lb ? 'selected' : '' }}>
                                {{ $lembaga->nama }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
             <div class="col-md-4">
                <form action="{{ route('pendaftaran.lembaga') }}" method="GET">
                    <label for="search" class="form-label fw-bold">Cari Nama / NIK</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" id="search" placeholder="Masukkan kata kunci..." value="{{ request('search') }}">
                        <input type="hidden" name="id_lembaga" value="{{ $selectedLembagaId }}">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                    </div>
                </form>
            </div>
             <div class="col-md-4">
                 <label class="form-label fw-bold">&nbsp;</label> <button id="btnPrintSelected" class="btn btn-action w-100" disabled>
                    <i class="bi bi-printer-fill"></i> Cetak Terpilih (<span id="selectedCount">0</span>)
                </button>
            </div>
        </div>

        <hr>

        <div class="row g-3 mt-3">
            <div class="col-md-4">
                <h5 class="mb-3">1. Unduh Template</h5>
                <a href="{{ route('template.pendaftaran.download') }}" class="btn btn-action d-block mb-2"><i class="bi bi-download"></i> Template Pendaftaran</a>
                <a href="{{ route('template.kasmasuk.download') }}" class="btn btn-action d-block"><i class="bi bi-download"></i> Template Kas Masuk</a>
            </div>

            <div class="col-md-8">
                <h5 class="mb-3">2. Unggah Data dari File</h5>
                <form id="form-upload-data">
                    <div class="mb-3">
                        <select class="form-select" id="upload_type" required>
                            <option value="" selected disabled>-- Pilih jenis data yang akan diunggah --</option>
                            <option value="pendaftaran_lembaga">Data Pendaftaran (Lembaga)</option>
                            <option value="kas_masuk">Data Kas Masuk</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <input class="form-control" type="file" id="file_upload" name="file" required>
                            <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Unggah File</button>
                        </div>
                        <div class="form-text">Untuk "Pendaftaran Lembaga", pastikan Anda telah memilih lembaga pada filter di atas.</div>
                    </div>
                </form>
            </div>
        </div>
        <div id="upload-result" class="alert mt-3" role="alert" style="display: none;"></div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="checkbox-col"><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                    <th>No</th>
                    <th>Nama Pendaftar</th>
                    <th>NIK</th>
                    <th>Nama Lembaga</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendaftar as $index => $p)
                    <tr>
                        <td class="checkbox-col">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $p->id }}">
                        </td>
                        <td>{{ $pendaftar->firstItem() + $index }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->nik }}</td>
                        <td>{{ $p->lembaga->nama ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center p-4">Tidak ada data yang ditemukan.</td>
                    </tr>
                @endempty
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{ $pendaftar->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Logika untuk Upload File (Tidak Berubah) ---
    async function handleUpload(form, url, resultDiv) {
        // ... (fungsi ini tetap sama persis)
    }

    const uploadForm = document.getElementById('form-upload-data');
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // ... (logika event listener ini tetap sama persis)
    });
    // ... (akhir dari logika upload) ...
    

    // --- JAVASCRIPT BARU UNTUK FITUR CETAK ---
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const printButton = document.getElementById('btnPrintSelected');
    const selectedCountSpan = document.getElementById('selectedCount');

    function getSelectedIds() {
        return Array.from(rowCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
    }

    function updatePrintButtonState() {
        const selectedIds = getSelectedIds();
        selectedCountSpan.textContent = selectedIds.length;
        printButton.disabled = selectedIds.length === 0;
    }

    selectAllCheckbox.addEventListener('change', function () {
        rowCheckboxes.forEach(checkbox => { checkbox.checked = this.checked; });
        updatePrintButtonState();
    });

    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            if (!this.checked) {
                selectAllCheckbox.checked = false;
            } else {
                if (getSelectedIds().length === rowCheckboxes.length) {
                    selectAllCheckbox.checked = true;
                }
            }
            updatePrintButtonState();
        });
    });

    printButton.addEventListener('click', async function () {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        this.disabled = true;
        this.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Memproses...`;

        try {
            const response = await fetch("{{ route('pendaftaran.cetak.batch') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/pdf', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ ids: ids })
            });
            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = 'laporan-pendaftar-terpilih.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();
            } else { 
                const errorData = await response.json().catch(() => ({ message: 'Gagal membaca respons error dari server.' }));
                let errorMessage = 'Gagal membuat laporan.\n\n';
                errorMessage += `Status: ${response.status} ${response.statusText}\n`;
                if (errorData.message) { errorMessage += `Pesan: ${errorData.message}\n`; }
                if (errorData.error_detail) { errorMessage += `Detail Teknis: ${errorData.error_detail}`; }
                alert(errorMessage);
            }
        } catch (error) { 
            alert('Terjadi kesalahan jaringan saat mencoba mencetak.'); 
        } finally {
            const currentSelectedCount = getSelectedIds().length;
            this.innerHTML = `<i class="bi bi-printer-fill"></i> Cetak Terpilih (${currentSelectedCount})`;
            updatePrintButtonState();
        }
    });

    updatePrintButtonState(); // Panggil saat halaman dimuat untuk inisialisasi
});
</script>
@endpush