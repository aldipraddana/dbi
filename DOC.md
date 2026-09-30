DANA KELUAR

=========================================================
1. STRUKTUR DATA
=========================================================
Tabel utama: dana_keluars
- id
- tanggal (date)
- jenis (enum: operasional, bahan_baku, gaji, biaya_kantor, biaya_lain)
- kategori_gaji (nullable enum: uang_makan, lembur, pelunasan_pekerjaan,
  kas_bon, gaji_bulanan)
- tipe_pekerja (nullable enum: harian, borongan)
- supplier_id (nullable, FK suppliers)
- kendaraan_id (nullable, FK kendaraans)
- karyawan_id (nullable, FK karyawans)
- client_id (nullable, FK clients)
- nama_proyek (nullable string)
- nama_mandor (nullable string)
- nama_pemborong (nullable string)
- nama_pekerjaan (nullable string)
- keperluan (nullable string)
- item (nullable string)          // biaya kantor / biaya lain
- qty (nullable decimal)
- nominal (decimal 15,2)          // nominal per item / nominal utama
- potongan_kas_bon (decimal 15,2 default 0)
- total (decimal 15,2)            // total akhir
- keterangan (nullable text)
- timestamps, softDeletes

Tabel detail (khusus bahan baku): dana_keluar_items
- id, dana_keluar_id (FK, cascade)
- pengadaan_stock_id (FK)
- nama_barang (string)
- supplier_id
- qty (decimal)
- harga_satuan (decimal 15,2)
- subtotal (decimal 15,2)

Buat model + relasi (DanaKeluar hasMany DanaKeluarItem, belongsTo
Supplier/Kendaraan/Karyawan/Client). Buat Enum PHP untuk jenis,
kategori_gaji, dan tipe_pekerja (implement HasLabel).

=========================================================
2. FORM (FILAMENT 3) - LEVEL 1
=========================================================
Field awal (selalu tampil):
- DatePicker "tanggal" (default today, required)
- Select "jenis" (live, required): Operasional, Bahan Baku, Gaji,
  Biaya Kantor, Biaya Lain-lain
  -> Saat jenis berubah, reset semua field level 2 (afterStateUpdated).

=========================================================
3. FORM - LEVEL 2 BERDASARKAN JENIS
=========================================================

A) OPERASIONAL
- Select kendaraan (relationship, searchable)
- Select karyawan (relationship, searchable)
- TextInput keperluan
- TextInput nominal (numeric, prefix "Rp", mask/format ribuan)

B) BAHAN BAKU
- Level 1 tambahan: Select supplier (live, required)
- Level 2: Repeater "items" (relasi ke dana_keluar_items):
  - Select "pengadaan_stock_id": opsi HANYA dari PengadaanStock milik
    supplier terpilih (filter berdasarkan supplier_id di form state);
    disabled jika supplier belum dipilih.
  - Saat barang dipilih (afterStateUpdated) otomatis isi & tampilkan:
    nama_barang, info supplier, qty (dari pengadaan stock) sebagai
    field read-only/Placeholder.
  - TextInput "harga_satuan" (numeric, live onBlur)
  - "subtotal" otomatis = qty x harga_satuan (read-only, dihitung ulang
    saat qty/harga berubah)
  - Di bawah repeater: "total" otomatis = sum semua subtotal
    (read-only, update setiap repeater berubah).
- Supplier di repeater otomatis mengikuti supplier level 1.

C) GAJI
- Level 1 tambahan: Select kategori_gaji (live, required):
  Uang Makan, Lembur, Pelunasan Pekerjaan, Kas Bon, Gaji Bulanan
- Level 2 sesuai kategori:

  1. Uang Makan & Lembur:
     - Select karyawan
     - TextInput nama_proyek (manual)
     - TextInput nominal

  2. Pelunasan Pekerjaan:
     - Select client
     - TextInput nama_mandor (manual)
     - TextInput nama_pekerjaan (manual)
     - TextInput nominal
     - Textarea keterangan

  3. Kas Bon:
     - Select tipe_pekerja (live): Harian / Borongan
     - Jika Harian: Select karyawan (filter karyawan harian), nominal
     - Jika Borongan: TextInput nama_pemborong (manual), nominal

  4. Gaji Bulanan:
     - Select tipe_pekerja (live): Harian / Borongan
     - Jika Harian: Select karyawan (filter harian), nominal
     - Jika Borongan: nama_proyek, nama_pemborong, nominal
       (SEMUA input manual)
     - Otomatis hitung potongan kas bon:
       * Cari total sisa kas bon (kas bon belum terpotong) berdasarkan
         karyawan_id (harian) atau nama_pemborong (borongan, match
         nama persis/case-insensitive).
       * Tampilkan Placeholder "Total Kas Bon Belum Lunas: Rp X".
       * Field "potongan_kas_bon" terisi otomatis (default = min(sisa
         kas bon, nominal)), tetap bisa diedit manual.
       * "total" = nominal - potongan_kas_bon (read-only, live).
     - Setelah data disimpan, tandai/catat potongan sebagai kas bon
       terbayar (buat tabel/kolom pelacakan, misal
       kas_bon_pelunasans atau kolom sisa pada record kas bon),
       sehingga sisa kas bon berkurang.

D) BIAYA KANTOR & BIAYA LAIN-LAIN
- TextInput item (manual)
- TextInput qty (numeric)
- TextInput nominal (numeric, harga satuan)
- "total" otomatis = qty x nominal
- Textarea keterangan

=========================================================
4. TABLE / LIST
=========================================================
Kolom: tanggal, jenis (badge berwarna), kategori gaji, ringkasan
(nama karyawan/supplier/item), total (format Rupiah, summarizer Sum).
Filter: rentang tanggal, jenis, kategori gaji.
Sort default: tanggal desc. Searchable pada kolom relevan.
Tambahkan header widget/total dana keluar per periode.

=========================================================
5. ATURAN TEKNIS
=========================================================
- Filament 3, Laravel 11 (sesuaikan versi project), PHP 8.2+.
- Gunakan Section/Grid agar layout rapi; komponen level 2 dibungkus
  Group/Section dengan ->visible(fn (Get $get) => ...).
- Gunakan ->dehydrated() / mutateFormDataBeforeSave untuk memastikan
  field yang tersembunyi tidak menyimpan data sisa (null-kan).
- Hitung ulang total di server (mutateFormDataBeforeCreate/Save)
  agar tidak bergantung pada state client.
- Gunakan DB::transaction saat menyimpan dana keluar + items + potongan
  kas bon.
- Validasi sesuai kondisi (required kondisional per jenis/kategori).
- Format mata uang Rupiah (Indonesia), label semua dalam Bahasa
  Indonesia.
- Buat file: Migration, Model, Enum, Resource (Pages: List/Create/Edit),
  dan jelaskan langkah menjalankan (php artisan migrate).

Output: berikan semua kode lengkap per file, berurutan, dengan
komentar singkat pada bagian logika reaktif.


--- OUTPUT

Modul Dana Keluar — Selesai
File yang dibuat (berurutan):
#	File	Keterangan
1	app/Traits/EnumTraits.php	Trait untuk semua Enum
2	app/Enums/DanaKeluarJenis.php	Enum: Operasional, Bahan Baku, Gaji, Biaya Kantor, Biaya Lain
3	app/Enums/KategoriGaji.php	Enum: Uang Makan, Lembur, Pelunasan Pekerjaan, Kas Bon, Gaji Bulanan
4	app/Enums/TipePekerja.php	Enum: Harian, Borongan
5	app/Models/DanaKeluar.php	Model utama + relasi + helper kas bon
6	app/Models/DanaKeluarItem.php	Model item bahan baku
7	database/migrations/..._add_jenis_to_karyawans_table.php	Tambah kolom jenis ke tabel karyawans
8	database/migrations/..._create_dana_keluar_tables.php	Tabel dana_keluars & dana_keluar_items
9	database/migrations/..._add_harga_satuan_to_pengadaan_stock_details_table.php	Tambah harga_satuan ke pengadaan_stock_details
10	app/Filament/Resources/DanaKeluarResource/Resource.php	Resource utama dengan semua form reaktif
11	app/Filament/Resources/DanaKeluarResource/Pages/ListDanaKeluars.php	Halaman list
12	app/Filament/Resources/DanaKeluarResource/Pages/CreateDanaKeluar.php	Halaman create + logic kas bon
13	app/Filament/Resources/DanaKeluarResource/Pages/ViewDanaKeluar.php	Halaman view
14	app/Filament/Resources/DanaKeluarResource/Pages/EditDanaKeluar.php	Halaman edit
15	app/Constants/UserMenuConstant.php	Tambah MENU_DANA_KELUAR
Logic Reaktif (Form):

Jenis (live) ─┬─ OPERASIONAL ──────► kendaraan, karyawan, keperluan, nominal
              ├─ BAHAN_BAKU ───────► supplier (wajib) → repeater barang-filtered-by-supplier
              ├─ GAJI ─────────────► kategori_gaji (live) ─┬─ Uang Makan/Lembur → karyawan, proyek, nominal
              │                                          ├─ Pelunasan → client, mandor, pekerjaan, nominal, keterangan
              │                                          ├─ Kas Bon ──► tipe_pekerja (live) → karyawan/nama_pemborong + nominal
              │                                          └─ Gaji Bulanan ──► tipe_pekerja (live) → auto-kalkulasi kas bon
              ├─ BIAYA_KANTOR ────► item, qty, nominal (total = qty × nominal)
              └─ BIAYA_LAIN ───────► item, qty, nominal (total = qty × nominal)
Langkah Menjalankan:

# 1. Jika composer dump-autoload belum selesai, jalankan manual:
composer dump-autoload

# 2. Migration sudah dijalankan otomatis. Jika perlu fresh:
php artisan migrate:fresh   # ⚠ HATI-HATI: menghapus semua data

# 3. Clear cache & config:
php artisan optimize:clear

# 4. Jalankan aplikasi:
php artisan serve
Menu Navigasi:
Transaksi → Dana Keluar (icon heroicon-o-arrow-down-left, sort ke-8)

Catatan Penting:
Karyawan perlu di-set jenis (harian/borongan) untuk fitur Gaji Bulanan & Kas Bon
PengadaanStockDetails perlu di-set harga_satuan agar barang muncul di Bahan Baku
Kas Bon tracking: saat Gaji Bulanan disimpan dengan potongan, sistem auto-mengurangi sisa kas bon pada record Kas Bon terkait (FIFO berdasarkan tanggal)