@extends('layouts.app')

@section('title', 'Data Pendaftar Perorangan - SITBA')

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
        transition: color 0.3s ease, transform 0.2s ease; 
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
        margin-bottom: 20px;
        color: var(--text-color);
        border-bottom: 1px solid rgba(0,0,0,0.1);
        padding-bottom: 10px;
    }
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
    .alert { 
        white-space: pre-wrap;
        word-break: break-all;
    }
    .btn-primary, .btn-success {
        background: var(--button-bg);
        border: none;
    }
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
    .btn-info {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
        color: white;
    }
    .btn-info:hover {
        background-color: #0b95b0;
    }
    /* CSS Kunci untuk lebar kolom dinamis */
    .table .no-wrap {
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<div class="content-page">
    <div class="page-header">
        <h1>Data Pendaftar Zakat (Perorangan)</h1>
        <a href="{{ route('landing') }}" class="btn-back" title="Kembali ke Menu Utama">
            <i class="bi bi-arrow-left-square-fill"></i>
        </a>
    </div>

    <div class="action-box">
        <!-- Baris untuk Filter, Pencarian -->
        <div class="row g-3 mb-4 align-items-end">
            <div class="col">
                <form action="{{ route('pendaftaran.perorangan') }}" method="GET">
                    <label for="search" class="form-label fw-bold">Cari Pendaftar</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" placeholder="Cari berdasarkan Nama, NIK..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                        <a href="{{ route('pendaftaran.perorangan') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <hr>
        
        <!-- Aksi Cetak & Hapus Digabung di sini -->
        <div class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text">Cetak data terbaru:</span>
                    <input type="number" class="form-control" id="printAmountInput" placeholder="Isi jumlah (misal: 50)" min="1" style="max-width: 200px;">
                    <button id="btnPrintCombined" class="btn btn-action text-white">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>
                <div class="form-text">Prioritas: Jika ada data yang dicentang, tombol akan mencetak yang dipilih.</div>
            </div>
            <div class="col-md-4">
                <button id="btnDeleteSelected" class="btn btn-danger w-100" disabled>
                    <i class="bi bi-trash-fill"></i> Hapus yang Dipilih (<span id="selectedCountDelete">0</span>)
                </button>
            </div>
        </div>
        
        <hr>
        
        <!-- Baris untuk Unduh & Unggah (Tetap sama) -->
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
                        <select class="form-select" id="upload_type" name="upload_type" required>
                            <option value="" disabled selected>-- Pilih jenis data --</option>
                            <option value="pendaftaran">Pendaftaran (Perorangan)</option>
                            <option value="kasmasuk">Kas Masuk</option>
                        </select>
                    </div>

                    <div id="kas-masuk-fields" style="display: none;">
                        <div class="mb-3">
                            <select class="form-select" id="object_zis" name="object_zis">
                                <option value="" disabled selected>-- Pilih Object ZIS --</option>
                                <option value="zakat">Zakat</option>
                                <option value="infaq">Infaq / Sedekah</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="bank-container" style="display: none;">
                            <select class="form-select" id="bank_rekening" name="bank_rekening">
                                <!-- Opsi diisi oleh JavaScript dari API -->
                            </select>
                        </div>

                        <div class="mb-3" id="uraian-container" style="display: none;">
                            <div class="input-group">
                                <select class="form-select" id="uraian_select">
                                    <!-- Opsi diisi oleh JavaScript dari API -->
                                </select>
                                <button class="btn btn-action" type="button" id="btn-add-uraian" title="Tambah Uraian Baru">+</button>
                                <button class="btn btn-danger" type="button" id="btn-delete-uraian" title="Hapus Uraian Terpilih" disabled>-</button>
                            </div>
                            <input type="hidden" id="uraian_final" name="uraian">
                        </div>

                        <div class="mb-3" id="add-uraian-container" style="display: none;">
                                <label for="new_uraian_text" class="form-label">Teks Uraian Baru:</label>
                                <div class="input-group">
                                    <input type="text" id="new_uraian_text" class="form-control" placeholder="Contoh: Zakat Perdagangan...">
                                    <button class="btn btn-success" type="button" id="btn-save-uraian">Simpan</button>
                                    <button class="btn btn-secondary" type="button" id="btn-cancel-add-uraian">Batal</button>
                                </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <input class="form-control" type="file" id="file_upload" name="file" required>
                            <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Unggah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id="upload-result" class="alert mt-4" role="alert" style="display: none;"></div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="checkbox-col"><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                    <th>No</th>
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
                    <th class="no-wrap">UPZ</th>
                    <th class="no-wrap">No Transaksi</th>
                    <th class="no-wrap">Via</th>
                    <th class="no-wrap">Zakat</th>
                    <th class="no-wrap">Infak</th>
                    <th class="no-wrap">Zakat Fitrah</th>
                    <th class="no-wrap">Jml Transaksi</th>
                    <th class="no-wrap">Tgl Transaksi</th>
                    <th class="no-wrap">Tgl Registrasi</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendaftar as $index => $p)
                    <tr>
                        <td class="checkbox-col"><input class="form-check-input row-checkbox" type="checkbox" value="{{ $p->id }}"></td>
                        <td>{{ $pendaftar->firstItem() + $index }}</td>
                        <td class="no-wrap">{{ $p->nama }}</td>
                        <td class="no-wrap">{{ $p->nik }}</td>
                        <td class="no-wrap">{{ $p->nip ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->npwp ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->npwz ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->handphone ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->email ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->telepon ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->format('d M Y') : '-' }}</td>
                        <td class="no-wrap">{{ $p->tempat_lahir ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->jenis_kelamin ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->alamat_rumah ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->alamat_korespondensi ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->pekerjaan ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->alamat_kantor ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->upz ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->no_transaksi ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->via ?? '-' }}</td>
                        <td class="no-wrap">{{ $p->zakat ? 'Rp ' . number_format($p->zakat, 0, ',', '.') : '-' }}</td>
                        <td class="no-wrap">{{ $p->infak ? 'Rp ' . number_format($p->infak, 0, ',', '.') : '-' }}</td>
                        <td class="no-wrap">{{ $p->zakat_fitrah ? 'Rp ' . number_format($p->zakat_fitrah, 0, ',', '.') : '-' }}</td>
                        <td class="no-wrap">{{ $p->jumlah_transaksi ? number_format($p->jumlah_transaksi, 0, ',', '.').' Kali' : '-' }}</td>
                        <td class="no-wrap">{{ $p->tgl_transaksi ? \Carbon\Carbon::parse($p->tgl_transaksi)->format('d M Y') : '-' }}</td>
                        <td class="no-wrap">{{ $p->tanggal_registrasi ? \Carbon\Carbon::parse($p->tanggal_registrasi)->format('d M Y') : '-' }}</td>
                        <td>{{ $p->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="28" class="text-center p-4">Tidak ada data yang ditemukan.</td>
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
    // --- Deklarasi Variabel ---
    const uploadForm = document.getElementById('form-upload-data');
    const resultDiv = document.getElementById('upload-result');
    const uploadTypeSelect = document.getElementById('upload_type');
    const kasMasukFields = document.getElementById('kas-masuk-fields');
    const objectZisSelect = document.getElementById('object_zis');
    const bankContainer = document.getElementById('bank-container');
    const bankSelect = document.getElementById('bank_rekening');
    const uraianContainer = document.getElementById('uraian-container');
    const uraianSelect = document.getElementById('uraian_select');
    const uraianFinal = document.getElementById('uraian_final');
    const btnAddUraian = document.getElementById('btn-add-uraian');
    const btnDeleteUraian = document.getElementById('btn-delete-uraian');
    const addUraianContainer = document.getElementById('add-uraian-container');
    const newUraianText = document.getElementById('new_uraian_text');
    const btnSaveUraian = document.getElementById('btn-save-uraian');
    const btnCancelAddUraian = document.getElementById('btn-cancel-add-uraian');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const deleteButton = document.getElementById('btnDeleteSelected');
    const selectedCountDeleteSpan = document.getElementById('selectedCountDelete');
    
    const printCombinedButton = document.getElementById('btnPrintCombined');
    const printAmountInput = document.getElementById('printAmountInput');

    let uraianData = {};
    let bankData = {};

    // --- SEMUA FUNGSI LAMA ANDA ADA DI SINI (LENGKAP) ---
    async function fetchInitialData() {
        try {
            const [uraianResponse, bankResponse] = await Promise.all([
                fetch("{{ url('/api/uraian') }}"),
                fetch("{{ url('/api/bank') }}")
            ]);
            if (!uraianResponse.ok) throw new Error('Gagal mengambil data uraian');
            if (!bankResponse.ok) throw new Error('Gagal mengambil data bank');
            uraianData = await uraianResponse.json();
            bankData = await bankResponse.json();
        } catch (error) {
            console.error(error);
            alert('Tidak dapat memuat data awal dari server.');
        }
    }

    function populateUraianSelect(kategori) {
        const options = uraianData[kategori] || [];
        uraianSelect.innerHTML = '';
        uraianSelect.appendChild(new Option('-- Pilih Uraian --', ''));
        options.forEach(option => {
            const optionElement = new Option(option.nama_uraian, option.nama_uraian);
            optionElement.dataset.id = option.id;
            uraianSelect.appendChild(optionElement);
        });
        uraianFinal.value = '';
        btnDeleteUraian.disabled = true;
    }

    function populateBankSelect(kategori) {
        const options = bankData[kategori] || [];
        bankSelect.innerHTML = '';
        bankSelect.appendChild(new Option('-- Pilih Bank Tujuan --', ''));
        options.forEach(bank => {
            const displayText = `${bank.nama_bank} - ${bank.nomor_rekening}`;
            bankSelect.appendChild(new Option(displayText, bank.id));
        });
    }

    if (uploadTypeSelect) {
        uploadTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            const isKasMasuk = selectedType === 'kasmasuk';
            kasMasukFields.style.display = isKasMasuk ? 'block' : 'none';
            objectZisSelect.required = isKasMasuk;
            bankSelect.required = isKasMasuk;
            uraianSelect.required = isKasMasuk;
            
            objectZisSelect.value = '';
            bankContainer.style.display = 'none';
            uraianContainer.style.display = 'none';
            addUraianContainer.style.display = 'none';
        });
    }

    if (objectZisSelect) {
        objectZisSelect.addEventListener('change', function() {
            const selectedKategori = this.value;
            const shouldShow = !!selectedKategori;
            bankContainer.style.display = shouldShow ? 'block' : 'none';
            uraianContainer.style.display = shouldShow ? 'block' : 'none';
            if (shouldShow) {
                populateBankSelect(selectedKategori);
                populateUraianSelect(selectedKategori);
            }
        });
    }

    if (uraianSelect) {
        uraianSelect.addEventListener('change', () => {
            uraianFinal.value = uraianSelect.value;
            btnDeleteUraian.disabled = !uraianSelect.value;
        });
    }

    if (btnAddUraian) {
        btnAddUraian.addEventListener('click', () => {
            uraianContainer.style.display = 'none';
            addUraianContainer.style.display = 'block';
            newUraianText.focus();
        });
    }
    if (btnCancelAddUraian) {
        btnCancelAddUraian.addEventListener('click', () => {
            addUraianContainer.style.display = 'none';
            uraianContainer.style.display = 'block';
            newUraianText.value = '';
        });
    }

    if (btnSaveUraian) {
        btnSaveUraian.addEventListener('click', async () => {
            const kategori = objectZisSelect.value;
            const namaUraian = newUraianText.value.trim();
            if (!kategori || !namaUraian) return alert('Kategori dan teks uraian baru harus diisi.');
            
            btnSaveUraian.disabled = true;
            btnSaveUraian.textContent = 'Menyimpan...';
            try {
                const response = await fetch("{{ url('/api/uraian') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ kategori: kategori, nama_uraian: namaUraian })
                });
                const newUraian = await response.json();
                if (!response.ok) {
                    let errorMsg = newUraian.message || 'Gagal menyimpan uraian.';
                    if(newUraian.errors && newUraian.errors.nama_uraian) errorMsg += `\nDetail: ${newUraian.errors.nama_uraian.join(', ')}`;
                    throw new Error(errorMsg);
                }
                if (!uraianData[kategori]) uraianData[kategori] = [];
                uraianData[kategori].push(newUraian);
                populateUraianSelect(kategori);
                uraianSelect.value = newUraian.nama_uraian;
                uraianFinal.value = newUraian.nama_uraian;
                btnDeleteUraian.disabled = false;
                btnCancelAddUraian.click();
            } catch (error) {
                alert(`Error: ${error.message}`);
            } finally {
                btnSaveUraian.disabled = false;
                btnSaveUraian.textContent = 'Simpan';
            }
        });
    }

    if (btnDeleteUraian) {
        btnDeleteUraian.addEventListener('click', async () => {
            const selectedOption = uraianSelect.options[uraianSelect.selectedIndex];
            const uraianId = selectedOption.dataset.id;
            const uraianText = selectedOption.value;
            if (!uraianId) return alert('Silakan pilih uraian yang ingin dihapus.');
            if (!confirm(`Anda yakin ingin menghapus uraian "${uraianText}" secara permanen?`)) return;

            btnDeleteUraian.disabled = true;
            try {
                const response = await fetch(`{{ url('/api/uraian') }}/${uraianId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal menghapus data.');
                
                const kategori = objectZisSelect.value;
                uraianData[kategori] = uraianData[kategori].filter(item => item.id != uraianId);
                populateUraianSelect(kategori);
                alert(result.message);
            } catch (error) {
                alert(`Error: ${error.message}`);
                btnDeleteUraian.disabled = false;
            }
        });
    }

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
                message = `✅ **Sukses:**\n${result.message || 'Proses berhasil.'}`;
                setTimeout(() => window.location.reload(), 3000);
            } else {
                resultDiv.className = 'alert alert-danger';
                message = `❌ **Error (Status: ${response.status}):**\n${result.message || 'Terjadi kesalahan.'}`;
                if (result.errors) message += `\n\n**Detail:**\n${JSON.stringify(result.errors, null, 2)}`;
            }
            resultDiv.textContent = message;
        } catch (error) {
            resultDiv.className = 'alert alert-danger';
            resultDiv.textContent = `❌ **Error Jaringan:**\n${error.message}`;
        } finally {
            resultDiv.style.display = 'block';
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="bi bi-upload"></i> Unggah';
        }
    }

    if(uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const uploadType = document.getElementById('upload_type').value;
            if (!uploadType) return alert('Silakan pilih jenis data yang akan diunggah.');
            if (this.querySelector('input[type="file"]').files.length === 0) return alert('Silakan pilih file untuk diunggah.');
            
            let targetUrl = '';
            if (uploadType === 'pendaftaran') {
                targetUrl = "{{ url('/api/pendaftaran-zakat/import/perorangan') }}";
            } else if (uploadType === 'kasmasuk') {
                targetUrl = "{{ url('/api/kas-masuk/import') }}";
            }
            if(targetUrl) handleUpload(this, targetUrl);
        });
    }

    // --- FUNGSI UNTUK AKSI MASAL (SELEKSI, HAPUS, CETAK) ---
    function getSelectedIds() {
        return Array.from(rowCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value, 10));
    }

    function updateActionButtonsState() {
        const selectedIds = getSelectedIds();
        const count = selectedIds.length;
        const areAnySelected = count > 0;

        if(selectedCountDeleteSpan) selectedCountDeleteSpan.textContent = count;
        if(deleteButton) deleteButton.disabled = !areAnySelected;

        if (printCombinedButton) {
            if (areAnySelected) {
                printCombinedButton.innerHTML = `<i class="bi bi-check2-square"></i> Cetak Terpilih (${count})`;
            } else {
                printCombinedButton.innerHTML = `<i class="bi bi-printer"></i> Cetak`;
            }
        }
    }

    if(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            rowCheckboxes.forEach(checkbox => { checkbox.checked = this.checked; });
            updateActionButtonsState();
        });
    }
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = getSelectedIds().length === rowCheckboxes.length;
            }
            updateActionButtonsState();
        });
    });
    if (printCombinedButton) {
        printCombinedButton.addEventListener('click', async function() {
            let idsToPrint = getSelectedIds();
            const amount = parseInt(printAmountInput.value, 10);
            let downloadFilename = 'laporan-pendaftar.pdf';

            if (idsToPrint.length > 0) {
                // PRIORITAS 1: Gunakan ID yang sudah dicentang
                downloadFilename = `laporan-pendaftar-terpilih.pdf`;
            } else if (amount > 0) {
                // PRIORITAS 2: Jika tidak ada yang dicentang, ambil ID dari tabel
                const allIdsOnPage = Array.from(rowCheckboxes).map(cb => parseInt(cb.value, 10));
                
                if (allIdsOnPage.length < amount) {
                    alert(`Hanya ada ${allIdsOnPage.length} data di halaman ini. Tidak bisa mencetak ${amount} data.`);
                    return;
                }
                
                idsToPrint = allIdsOnPage.slice(0, amount);
                downloadFilename = `laporan-pendaftar-terbaru-${amount}-data.pdf`;

            } else {
                alert('Silakan pilih data dengan checkbox, atau isi jumlah data yang akan dicetak.');
                return;
            }
            
            if (idsToPrint.length === 0) {
                alert('Tidak ada data yang dipilih untuk dicetak.');
                return;
            }

            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Mencetak...`;

            try {
                const response = await fetch("{{ route('pendaftaran.cetak.batch') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/pdf',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: idsToPrint }) // Selalu kirim 'ids'
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none'; a.href = url;
                    a.download = downloadFilename;
                    document.body.appendChild(a); a.click();
                    window.URL.revokeObjectURL(url); a.remove();
                } else {
                    const errorData = await response.json().catch(() => ({ message: 'Gagal membuat laporan.' }));
                    alert(`Error: ${errorData.message}`);
                }
            } catch (error) {
                alert('Terjadi kesalahan jaringan saat mencetak.');
            } finally {
                this.disabled = false;
                this.innerHTML = originalText;
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
    
    // Panggil semua fungsi inisialisasi
    fetchInitialData();
    updateActionButtonsState();
});
</script>
@endpush
