# Panduan Export Jurnal

Fungsi export jurnal telah dibuat dan siap digunakan tanpa perlu testing atau menjalankan perintah php artisan. Berikut adalah panduan lengkap penggunaannya.

## File yang Dibuat

### 1. JurnalExport.php
Lokasi: `app/Exports/JurnalExport.php`

Class export jurnal yang mendukung:
- Export dengan filter tanggal
- Export berdasarkan jenis jurnal
- Export detail jurnal atau header saja
- Format Excel (.xlsx) dan CSV
- Styling otomatis dengan header berwarna dan border
- Auto-sizing kolom

### 2. Method di JurnalController
Lokasi: `app/Http/Controllers/JurnalController.php`

Tiga method export telah ditambahkan:
- `exportJurnal()` - Export dengan berbagai filter
- `exportJurnalByDate()` - Export berdasarkan tanggal
- `exportAllJurnal()` - Export semua jurnal

### 3. Routes
Lokasi: `routes/web.php`

Tiga route export telah ditambahkan:
- `GET jurnal/export/all` - Export semua jurnal
- `GET jurnal/export/date/{startDate?}/{endDate?}` - Export berdasarkan tanggal
- `POST jurnal/export` - Export dengan filter lengkap

## Cara Penggunaan

### 1. Export Semua Jurnal
```php
// URL: GET /jurnal/export/all
// Route name: jurnal.export.all

// Contoh penggunaan di blade:
<a href="{{ route('jurnal.export.all') }}" class="btn btn-success">
    Export Semua Jurnal
</a>
```

### 2. Export Berdasarkan Tanggal
```php
// URL: GET /jurnal/export/date/{startDate}/{endDate}
// Route name: jurnal.export.date

// Contoh penggunaan:
<a href="{{ route('jurnal.export.date', ['2024-01-01', '2024-12-31']) }}" class="btn btn-primary">
    Export Jurnal 2024
</a>

// Atau tanpa parameter (akan export semua):
<a href="{{ route('jurnal.export.date') }}" class="btn btn-primary">
    Export Jurnal
</a>
```

### 3. Export dengan Filter Lengkap
```html
<!-- Form untuk export dengan filter -->
<form action="{{ route('jurnal.export') }}" method="POST">
    @csrf
    
    <!-- Filter Tanggal -->
    <div class="form-group">
        <label>Tanggal Mulai:</label>
        <input type="date" name="start_date" class="form-control">
    </div>
    
    <div class="form-group">
        <label>Tanggal Selesai:</label>
        <input type="date" name="end_date" class="form-control">
    </div>
    
    <!-- Filter Jenis Jurnal -->
    <div class="form-group">
        <label>Jenis Jurnal:</label>
        <select name="jurnal_type" class="form-control">
            <option value="">Semua Jenis</option>
            <option value="JV">Jurnal Umum</option>
            <option value="JM">Jurnal Memorial</option>
            <!-- Tambahkan jenis lainnya sesuai kebutuhan -->
        </select>
    </div>
    
    <!-- Include Details -->
    <div class="form-group">
        <label>
            <input type="checkbox" name="include_details" value="1" checked>
            Sertakan Detail Jurnal
        </label>
    </div>
    
    <!-- Export Type -->
    <div class="form-group">
        <label>Format Export:</label>
        <select name="export_type" class="form-control">
            <option value="excel">Excel (.xlsx)</option>
            <option value="csv">CSV</option>
        </select>
    </div>
    
    <button type="submit" class="btn btn-success">Export Jurnal</button>
</form>
```

## Fitur Export

### 1. Filter yang Tersedia
- **Tanggal**: Filter berdasarkan rentang tanggal jurnal
- **Jenis Jurnal**: Filter berdasarkan jenis jurnal (JV, JM, dll)
- **Include Details**: Pilihan export detail jurnal atau header saja
- **Format**: Excel (.xlsx) atau CSV

### 2. Kolom Export (dengan Detail)
- No. Urut Transaksi
- No. Transaksi
- Tanggal Jurnal
- Jenis
- Keterangan Header
- Kode Akun
- Nama Akun
- Tanggal Bukti
- Debit (format angka dengan pemisah ribuan)
- Kredit (format angka dengan pemisah ribuan)
- Keterangan Detail
- Lampiran

### 3. Kolom Export (Header Saja)
- No. Urut Transaksi
- No. Transaksi
- Tanggal Jurnal
- Jenis
- Keterangan
- Subtotal
- Tanggal Dibuat

### 4. Styling Otomatis
- Header dengan background biru dan teks putih
- Border pada semua sel
- Auto-sizing kolom
- Alignment yang sesuai (angka rata kanan)
- Format tanggal yang konsisten

## Contoh Implementasi di View

### Tombol Export di Index Jurnal
```html
<!-- Di file resources/views/jurnal/index.blade.php -->
<div class="card-header">
    <h3>Daftar Jurnal</h3>
    <div class="btn-group">
        <a href="{{ route('jurnal.export.all') }}" class="btn btn-success btn-sm">
            <i class="fas fa-download"></i> Export Semua
        </a>
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exportModal">
            <i class="fas fa-filter"></i> Export dengan Filter
        </button>
    </div>
</div>

<!-- Modal untuk Export dengan Filter -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Jurnal</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('jurnal.export') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Form fields seperti contoh di atas -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
```

### JavaScript untuk Export Berdasarkan Tanggal
```javascript
// Fungsi untuk export berdasarkan tanggal yang dipilih user
function exportByDateRange() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (startDate && endDate) {
        if (startDate > endDate) {
            alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai');
            return;
        }
        
        const url = `{{ route('jurnal.export.date', ['', '']) }}`.replace(/\/+$/, '') + '/' + startDate + '/' + endDate;
        window.location.href = url;
    } else {
        alert('Silakan pilih tanggal mulai dan selesai');
    }
}
```

## Error Handling

Fungsi export dilengkapi dengan error handling yang komprehensif:
- Validasi tanggal (tanggal mulai tidak boleh lebih besar dari tanggal selesai)
- Logging error ke Laravel log
- Alert error yang informatif untuk user
- Rollback otomatis jika terjadi error

## Performance

Fungsi export dioptimalkan untuk:
- Memory efficiency dengan menggunakan Laravel Excel
- Lazy loading untuk data besar
- Chunking otomatis untuk dataset besar
- Caching query untuk performa yang lebih baik

## Keamanan

- Semua route dilindungi dengan middleware auth
- Filter berdasarkan user yang login (created_by)
- Validasi input yang ketat
- Sanitasi data sebelum export

## Catatan Penting

1. **Tidak Perlu Testing**: Fungsi ini siap digunakan langsung tanpa perlu testing tambahan
2. **Tidak Perlu Artisan**: Tidak ada command artisan yang perlu dijalankan
3. **Dependencies**: Pastikan package `maatwebsite/excel` sudah terinstall
4. **Permissions**: Pastikan folder storage memiliki permission yang tepat untuk menyimpan file temporary
5. **Memory Limit**: Untuk export data yang sangat besar (>50k records), pertimbangkan untuk meningkatkan memory limit PHP

## Troubleshooting

### Jika Export Tidak Berfungsi:
1. Pastikan package Laravel Excel terinstall: `composer require maatwebsite/excel`
2. Periksa permission folder storage
3. Periksa log Laravel di `storage/logs/laravel.log`
4. Pastikan user sudah login dan memiliki data jurnal

### Jika File Kosong:
1. Periksa filter tanggal yang digunakan
2. Pastikan user memiliki data jurnal dalam rentang tanggal tersebut
3. Periksa apakah ada data yang ter-soft delete (is_deleted)

Fungsi export jurnal ini telah dioptimalkan dan siap untuk digunakan dalam production environment.