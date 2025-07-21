@extends('layouts.app')

@section('title', 'Data Pendaftar Lembaga - SITBA')

@push('styles')
<style>
    /* Kontainer utama untuk halaman data */
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
    /* Header halaman */
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
    /* Tabel */
    .table thead th { 
        background-color: var(--subtitle-color); 
        color: #ffffff; 
        vertical-align: middle;
    }
    .table th.checkbox-col, .table td.checkbox-col { 
        width: 1%; 
        text-align: center; 
        vertical-align: middle;
    }
    /* Panel Aksi */
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
    /* Tombol Aksi Umum */
    .btn-action {
        background: var(--button-bg);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
    }
    .btn-action:hover {
        background: var(--button-hover);
        color: white;
    }
    /* Override tombol Bootstrap jika perlu */
    .btn-primary, .btn-success {
        background: var(--button-bg);
        border: none;
    }
    /* Tombol Hapus */
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
    /* Tombol Info untuk Unduh */
    .btn-info {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
        color: white;
    }
    .btn-info:hover {
        background-color: #0b95b0;
    }
    .table .no-wrap {
        white-space: nowrap;
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

    <!-- --- PANEL AKSI & FILTER TERPADU --- -->
    <div class="action-box">
        <div class="row g-3 mb-4 align-items-end">
            <!-- Filter Lembaga -->
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
             <!-- Pencarian -->
            <div class="col-md-4">
                <form action="{{ route('pendaftaran.lembaga') }}" method="GET">
                    <label for="search" class="form-label fw-bold">Cari</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" id="search" placeholder="Masukkan kata kunci..." value="{{ request('search') }}">
                        <input type="hidden" name="id_lembaga" value="{{ $selectedLembagaId }}">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                    </div>
                </form>
            </div>
             <!-- Aksi Massal (Cetak & Hapus) -->
            <div class="col-md-4">
                <label class="form-label fw-bold">Aksi Massal</label>
                <div class="btn-group w-100" role="group">
                    <button id="btnPrintSelected" class="btn btn-action" disabled>
                       <i class="bi bi-printer-fill"></i> Cetak (<span id="selectedCountPrint">0</span>)
                   </button>
                   <button id="btnDeleteSelected" class="btn btn-danger" disabled>
                       <i class="bi bi-trash-fill"></i> Hapus (<span id="selectedCountDelete">0</span>)
                   </button>
                </div>
            </div>
        </div>

        <hr>

        <!-- Baris untuk Unduh & Unggah -->
        <div class="row g-3 mt-3">
            <div class="col-md-4">
                <h5 class="mb-3">1. Unduh Template</h5>
                <a href="{{ route('template.pendaftaran.download') }}" class="btn btn-action d-block mb-2"><i class="bi bi-download"></i> Template Pendaftaran</a>
                <a href="{{ route('template.kasmasuk.download') }}" class="btn btn-action d-block"><i class="bi bi-download"></i> Template Kas Masuk</a>
            </div>
            <div class="col-md-8">
                <h5 class="mb-3">2. Unggah Data dari File</h5>
                <form id="form-upload-data" onsubmit="return false;">
                    <div class="mb-3">
                        <select class="form-select" id="upload_type" required>
                            <option value="" selected disabled>-- Pilih jenis data --</option>
                            <option value="pendaftaran_lembaga">Pendaftaran (Lembaga)</option>
                            <option value="kas_masuk">Kas Masuk</option>
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
    <!-- --- AKHIR PANEL AKSI --- -->

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="checkbox-col"><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                    <th class="no-wrap">No</th>
                    <th class="no-wrap">Nama</th>
                    <th class="no-wrap">NIK</th>
                    <th class="no-wrap">NIP</th>
                    <th class="no-wrap">NPWP</th>
                    <th class="no-wrap">NPWZ</th>
                    <th class="no-wrap">Handphone</th>
                    <th class="no-wrap">Email</th>
                    <th class="no-wrap">Telepon</th>
                    <th class="no-wrap">Tgl Lahir</th>
                    <th class="no-wrap">Tempat Lahir</th>
                    <th class="no-wrap">Gender</th>
                    <th class="no-wrap">Alamat Rumah</th>
                    <th class="no-wrap">Alamat Korespondensi</th>
                    <th class="no-wrap">Pekerjaan</th>
                    <th class="no-wrap">Alamat Kantor</th>
                    <th class="no-wrap">Nama Lembaga</th>
                    <th class="no-wrap">UPZ</th>
                    <th class="no-wrap">No Transaksi</th>
                    <th class="no-wrap">Zakat</th>
                    <th class="no-wrap">Infak</th>
                    <th class="no-wrap">Zakat Fitrah</th>
                    <th class="no-wrap">Jml Transaksi</th>
                    <th class="no-wrap">Tgl Transaksi</th>
                    <th class="no-wrap">Tgl Registrasi</th>
                    <th class="no-wrap">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendaftar as $index => $p)
                    <tr>
                        <td class="checkbox-col">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $p->id }}">
                        </td>
                        <td class="no-wrap">{{ $pendaftar->firstItem() + $index }}</td>
                        <th class="no-wrap">{{ $p->nama }}</td>
                        <th class="no-wrap">{{ $p->nik }}</td>
                        <th class="no-wrap">{{ $p->nip ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->npwp ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->npwz ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->handphone ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->email ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->telepon ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->format('d-m-Y') : '-' }}</td>
                        <th class="no-wrap">{{ $p->tempat_lahir ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->jenis_kelamin ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->alamat_rumah ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->alamat_korespondensi ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->pekerjaan ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->alamat_kantor ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->lembaga->nama ?? 'N/A' }}</td>
                        <th class="no-wrap">{{ $p->upz ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->no_transaksi ?? '-' }}</td>
                        <th class="no-wrap">{{ $p->zakat ? 'Rp ' . number_format($p->zakat, 0, ',', '.') : '-' }}</td>
                        <th class="no-wrap">{{ $p->infak ? 'Rp ' . number_format($p->infak, 0, ',', '.') : '-' }}</td>
                        <th class="no-wrap">{{ $p->zakat_fitrah ? 'Rp ' . number_format($p->zakat_fitrah, 0, ',', '.') : '-' }}</td>
                        <th class="no-wrap">{{ $p->jumlah_transaksi ? number_format($p->jumlah_transaksi, 0, ',', '.').' Kali' : '-' }}</td>
                        <th class="no-wrap">{{ $p->tgl_transaksi ? \Carbon\Carbon::parse($p->tgl_transaksi)->format('d-m-Y') : '-' }}</td>
                        <th class="no-wrap">{{ $p->tanggal_registrasi ? \Carbon\Carbon::parse($p->tanggal_registrasi)->format('d-m-Y') : '-' }}</td>
                        <td class="no-wrap">{{ $p->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="27" class="text-center p-4">Tidak ada data yang ditemukan.</td>
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
    const uploadForm = document.getElementById('form-upload-data');
    const resultDiv = document.getElementById('upload-result');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const printButton = document.getElementById('btnPrintSelected');
    const deleteButton = document.getElementById('btnDeleteSelected');
    const selectedCountPrintSpan = document.getElementById('selectedCountPrint');
    const selectedCountDeleteSpan = document.getElementById('selectedCountDelete');

    // --- FUNGSI UPLOAD ---
    async function handleUpload(form, url) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        resultDiv.style.display = 'none';
        submitButton.disabled = true;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Mengunggah...`;

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            });
            const result = await response.json();
            
            let message = '';
            if (response.ok) {
                resultDiv.className = 'alert alert-success';
                message = `✅ **Sukses:**\n${result.message || 'Proses impor berhasil.'}`;
                if(result.data) {
                    message += `\n\nDetail:\n- Dibuat: ${result.data.records_created || 0}\n- Diperbarui: ${result.data.records_updated || 0}\n- Gagal: ${result.data.records_failed || 0}`;
                }
                setTimeout(() => location.reload(), 4000);
            } else {
                resultDiv.className = 'alert alert-danger';
                message = `❌ **Error (Status: ${response.status}):**\n${result.message || 'Terjadi kesalahan.'}`;
                if (result.errors || result.failures) {
                    message += `\n\n**Detail Kesalahan:**\n${JSON.stringify(result.errors || result.failures, null, 2)}`;
                }
            }
            resultDiv.textContent = message;
        } catch (error) {
            resultDiv.className = 'alert alert-danger';
            resultDiv.textContent = `❌ **Error Jaringan:**\n${error.message}`;
        } finally {
            resultDiv.style.display = 'block';
            submitButton.disabled = false;
            submitButton.innerHTML = `<i class="bi bi-upload"></i> Unggah File`;
        }
    }

    if(uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const uploadType = document.getElementById('upload_type').value;
            if (!uploadType) return alert('Silakan pilih jenis data yang akan diunggah!');
            if (this.querySelector('input[type="file"]').files.length === 0) return alert('Silakan pilih file untuk diunggah.');

            let targetUrl = '';
            if (uploadType === 'pendaftaran_lembaga') {
                const idLb = document.getElementById('filter_lembaga').value;
                if (!idLb) return alert('Untuk mengunggah data pendaftaran, silakan pilih lembaga terlebih dahulu!');
                targetUrl = `{{ url('/api/pendaftaran-zakat/import/lembaga') }}/${idLb}`;
            } else if (uploadType === 'kas_masuk') {
                targetUrl = `{{ url('/api/kas-masuk/import') }}`;
            }
            
            if(targetUrl) handleUpload(this, targetUrl);
        });
    }

    // --- Logika Aksi Massal (Cetak & Hapus) ---
    function getSelectedIds() {
        return Array.from(rowCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
    }

    function updateActionButtonsState() {
        const selectedIds = getSelectedIds();
        const count = selectedIds.length;
        const areAnySelected = count > 0;

        if(selectedCountPrintSpan) selectedCountPrintSpan.textContent = count;
        if(selectedCountDeleteSpan) selectedCountDeleteSpan.textContent = count;
        
        if(printButton) printButton.disabled = !areAnySelected;
        if(deleteButton) deleteButton.disabled = !areAnySelected;
    }

    if(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            rowCheckboxes.forEach(checkbox => { checkbox.checked = this.checked; });
            updateActionButtonsState();
        });
    }

    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            selectAllCheckbox.checked = getSelectedIds().length === rowCheckboxes.length;
            updateActionButtonsState();
        });
    });

    if(printButton) {
        printButton.addEventListener('click', async function () {
            const ids = getSelectedIds();
            if (ids.length === 0) return;
            this.disabled = true;
            this.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Mencetak...`;
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
                    a.style.display = 'none'; a.href = url;
                    a.download = 'laporan-pendaftar-terpilih.pdf';
                    document.body.appendChild(a); a.click();
                    window.URL.revokeObjectURL(url); a.remove();
                } else { 
                    const errorData = await response.json().catch(() => ({ message: 'Gagal membaca respons error.' }));
                    alert(`Gagal membuat laporan: ${errorData.message}`);
                }
            } catch (error) { 
                alert('Terjadi kesalahan jaringan saat mencetak.'); 
            } finally {
                this.innerHTML = `<i class="bi bi-printer-fill"></i> Cetak (<span id="selectedCountPrint">${getSelectedIds().length}</span>)`;
                updateActionButtonsState();
            }
        });
    }
    
    if(deleteButton) {
        deleteButton.addEventListener('click', async function () {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            const isConfirmed = confirm(`Anda yakin ingin menghapus ${ids.length} data yang dipilih secara permanen? Aksi ini tidak dapat dibatalkan.`);
            if (!isConfirmed) return;

            this.disabled = true;
            this.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menghapus...`;

            try {
                const response = await fetch("{{ route('pendaftaran.hapus.batch') }}", {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ ids: ids })
                });

                const result = await response.json();
                if (response.ok) {
                    alert(result.message || 'Data berhasil dihapus.');
                    location.reload();
                } else {
                    alert(`Error: ${result.message || 'Terjadi kesalahan saat menghapus data.'}`);
                }
            } catch (error) {
                alert('Error Jaringan: Gagal menghubungi server.');
            } finally {
                this.innerHTML = `<i class="bi bi-trash-fill"></i> Hapus (<span id="selectedCountDelete">${getSelectedIds().length}</span>)`;
                updateActionButtonsState();
            }
        });
    }
    
    updateActionButtonsState(); // Inisialisasi awal
});
</script>
@endpush
