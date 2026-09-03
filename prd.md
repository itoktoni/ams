**KIRO ASSET MANAGEMENT SYSTEM**

(Functional Specification Document)

|     |     |
| --- | --- |
| Subjek | Divisi Umum & Administrasi - Pengelolaan Asset Perusahaan |
| Tempat | Seluruh Cabang Kiro |
| Peserta | Admin Asset, Teknisi, Manager, Staff Umum, Finance, Procurement |
| Deskripsi | Sistem manajemen asset terintegrasi untuk pengelolaan siklus hidup asset dari pembelian hingga penghapusan, termasuk penyusutan, tiket perawatan, peminjaman, pergerakan, sparepart, dan service berkala. |
| Versi | 1.0 |

# INFORMASI UMUM

## 1\. PENDAHULUAN

Perusahaan Kiro mengelola ribuan asset yang tersebar di seluruh cabang, mulai dari kendaraan operasional, peralatan kantor, hingga infrastruktur teknologi. Saat ini, pengelolaan aset masih mengandalkan spreadsheet manual dan prosedur berbasis kertas yang rentan terhadap kesalahan data, keterlambatan pelaporan, dan hilangnya jejak riwayat perawatan. Kondisi ini mengakibatkan penyusutan tidak akurat, asset hilang tanpa jejak, biaya maintenance membengkak, dan risiko denda akibat dokumen (SIM, STNK, subscription) yang tidak diperpanjang tepat waktu.

Dengan pertumbuhan jumlah aset yang terus meningkat dan distribusi di berbagai lokasi, diperlukan sebuah sistem informasi terpusat yang mampu melacak seluruh siklus hidup aset secara end-to-end, mengotomasi proses perawatan, dan memberikan visibilitas real-time kepada semua pemangku kepentingan.

## 2\. Tujuan Aplikasi

Sistem ini bertujuan untuk menyediakan platform terpadu yang mengotomasi pengelolaan siklus hidup asset, mulai dari pengadaan, pendaftaran, penggunaan, perawatan, hingga penghapusan. Sistem ini memberikan visibilitas real-time terhadap kondisi asset, mengoptimalkan biaya operasional melalui data yang akurat, dan memastikan kepatuhan terhadap regulasi serta SLA internal.

# KEBUTUHAN

## 1\. Penggunaan Sistem

Sistem ini digunakan oleh berbagai peran di seluruh cabang untuk memastikan pengelolaan asset berjalan efektif dan transparan.

|     |     |     |
| --- | --- | --- |
| **No** | **User** | **Deskripsi** |
| 1   | Admin Asset | Mengelola data master asset, registrasi, penghapusan, dan pemutakhiran data. Bertanggung jawab atas keakuratan data inventaris seluruh cabang. |
| 2   | Teknisi | Menerima dan menyelesaikan tiket kerusakan/maintenance, melakukan service berkala, dan melaporkan penggunaan sparepart. |
| 3   | Manager | Menyetujui pembelian asset (PO), penghapusan asset, peminjaman, dan melakukan monitoring dashboard performa asset serta biaya operasional. |
| 4   | Staff Umum | Mengajukan peminjaman asset, membuat tiket kerusakan, dan melihat status asset yang dimiliki. |
| 5   | Accounting | Memantau penyusutan asset, melakukan rekonsiliasi dengan buku besar, dan menganalisis total cost of ownership (TCO). |
| 6   | Procurement | Mengelola purchase order asset, penerimaan asset, dan integrasi dengan vendor sparepart serta service. |

## 2\. Penjelasan Fungsional Umum

Alur kerja utama sistem menggambarkan siklus hidup asset dari awal hingga akhir, dengan checkpoint di setiap tahapan fungsional.

|     |     |     |
| --- | --- | --- |
| **No** | **Nama** | **Deskripsi** |
| 1   | Pembelian & Penerimaan | Proses pengadaan asset melalui Purchase Order, penerimaan asset ke gudang, pengeluaran asset dari gudang dan registrasi awal asset ke dalam sistem. |
| 2   | Registrasi Asset | Pendaftaran asset baru dengan data lengkap: nama, kategori, lokasi, nilai perolehan, umur ekonomis asset, dan dokumen pendukung. |
| 3   | Penyusutan Asset | Perhitungan otomatis penyusutan asset berdasarkan metode garis lurus. |
| 4   | Peminjaman Asset | Proses peminjaman dengan auto-expiry, reminder, denda otomatis, dan auto-lock asset saat overdue. |
| 5   | Pergerakan Asset (Movement) | Pencatatan perpindahan asset antar lokasi/cabang dengan approval dan jejak audit. |
| 6   | Tiket Kerusakan Asset | Pelaporan kerusakan asset, assignment ke teknisi berdasarkan zona dan skill, serta tracking status pengerjaan. |
| 7   | Maintenance & Service Berkala | Penjadwalan dan pelaksanaan perawatan rutin berdasarkan interval waktu atau penggunaan. |
| 8   | Alert & Notifikasi | Sistem notifikasi otomatis untuk expiry SIM/STNK/subscription dan jatuh tempo service berkala. |
| 9   | Sparepart & Inventory | Pengelolaan stok sparepart, auto-replenishment, dan tracking penggunaan per tiket. |
| 10  | Penghapusan Asset | Proses penghapusan asset afkir/rusak dengan dual-approval, dokumentasi, dan reverse logistics. |
| 11  | Penjualan Asset | Proses penjualan asset untuk peremajaan asset ataupun untuk pengalihan kepemilikan harta atau kekayaan (seperti tanah, bangunan, mesin, atau kendaraan) dari satu pihak ke pihak lain dengan imbalan uang atau kompensasi. |

## 3\. Kebutuhan Hardware and Software

|     |     |     |
| --- | --- | --- |
| **No** | **Nama** | **Deskripsi** |
| 1   | Server Aplikasi | Minimal 4 CPU cores, 8 GB RAM, 100 GB SSD - untuk menjalankan backend dan database |
| 2   | Database Server | PostgreSQL 15+ atau MySQL 8+ dengan replikasi untuk high availability |
| 3   | Web Server / Reverse Proxy | Nginx atau Apache untuk serving frontend dan API gateway |
| 4   | Frontend Client | Browser modern (Chrome/Firefox/Edge) - desktop dan mobile responsive |
| 5   | Mobile Device (opsional) | Smartphone/tablet untuk teknisi - scanning QR code, foto bukti, GPS check-in |
| 6   | Barcode/QR Scanner (opsional) | Hardware scanner atau kamera mobile untuk identifikasi aset cepat |
| 7   | Framework Backend | Laravel 10+ (PHP 8.2+) atau framework modern lainnya dengan RESTful API |
| 8   | Framework Frontend | React/Vue.js dengan Tailwind CSS untuk UI responsive |
| 9   | Queue Worker | Redis + Laravel Queue untuk background job (penyusutan, notifikasi, scheduler) |
| 10  | Email/SMS Gateway | Integrasi dengan provider notifikasi untuk alert otomatis |

# MODULE ASSET

|     |     |
| --- | --- |
| **Module** | **Deskripsi** |
| Registrasi Asset | Penambahan asset baru dengan data: nama, kategori, merek, model, serial number, tanggal perolehan, harga perolehan, lokasi, penanggung jawab, foto, dan dokumen pendukung. |
| Master Data Asset | Database sentral seluruh asset dengan pencarian, filter (lokasi/kategori/status), dan export data. |
| Kategori & Klasifikasi | Pengelompokan asset berdasarkan jenis (kendaraan, elektronik, furniture, IT), sub-kategori, dan kode asset unik. |
| Status Asset | Tracking status: Aktif, Dipinjam, Maintenance, Rusak, Dihapus, Afkir. Transisi status teraudit. |
| Dokumen Asset | Penyimpanan digital dokumen: BPKB, STNK, SIM, invoice, garansi, dan sertifikat lainnya. |
| QR Code / Barcode | Generate kode unik per asset untuk identifikasi cepat via scanning. |
| Dashboard Asset | Tampilan ringkasan: total asset, asset per kategori, asset per lokasi, asset mendekati habis umur ekonomis, dan grafik distribusi. |

# MODULE PENYUSUTAN

|     |     |
| --- | --- |
| **Module** | **Deskripsi** |
| Konfigurasi Metode | Pengaturan metode penyusutan: Garis Lurus (Straight Line) atau metode lain sesuai kebijakan akuntansi yang diterapkan. |
| Parameter Penyusutan | Pengaturan masa manfaat, dan tanggal mulai penyusutan per asset atau per kategori. |
| Perhitungan Otomatis | Scheduler bulanan menghitung beban penyusutan secara otomatis berdasarkan metode garis lurus dan sesuai dengan kelompok penyusutan DJP. |
| Depreciation Ledger | Append-only ledger dengan hash chain per asset - setiap entry tidak bisa diubah atau dihapus, hanya bisa dikoreksi dengan reversal entry. |
| Replay & Audit | Auditor bisa merekonstruksi nilai buku hari ini dari harga perolehan dengan replay seluruh entry ledger. Verifikasi integrity via hash chain. (secara teknisnya gw belom paham ini fungsinya) |
| Laporan Penyusutan | Report bulanan/tahunan: beban penyusutan per departemen, akumulasi penyusutan, nilai buku per asset. |
| Simulasi What-If | Mode simulasi untuk membandingkan dampak penggunaan metode berbeda terhadap laba 5 tahun ke depan tanpa menulis ke ledger. (secara teknisnya gw belom paham ini fungsinya) |

# MODULE TIKET & MAINTENANCE

|     |     |
| --- | --- |
| **Module** | **Deskripsi** |
| Pembuatan Tiket | Staff umum/teknisi membuat tiket kerusakan dengan detail: asset terdampak, deskripsi masalah, foto/video bukti, tingkat urgensi, dan lokasi. |
| State Machine Tiket | Alur status: Open, Assigned, In Progress, Waiting Parts, Completed, Verified. Setiap transisi teraudit dengan timestamp dan actor. |
| SLA & Auto-Escalation | SLA timeout per severity (Critical: 4h, High: 8h, Medium: 24h, Low: 72h). Auto-escalate ke atasan jika SLA terlewati. |
| Geo-Batching Assignment | Tiket di-batch berdasarkan cluster zona geografis + skill teknisi. Optimasi rute harian untuk efisiensi perjalanan teknisi. |
| Fast-Lane Urgent | Tiket severity Critical bypass batching, langsung di-assign ke teknisi terdekat dengan skill yang sesuai. |
| Aging Weight | Tiket yang menunggu >70% SLA otomatis naik bobot dan memaksa cluster terbentuk di sekitarnya, mencegah starvation. |
| Bukti Pengerjaan | Foto before/after + timestamp + GPS teknisi yang mengunci klaim garansi dan menolak klaim palsu. |
| Rekap & Analytics | Dashboard performa: MTTR, tiket per zona, beban teknisi, trend kerusakan per asset/kategori. |

# MODULE MAINTENANCE BERDASARKAN TEKNISI

|     |     |
| --- | --- |
| **Module** | **Deskripsi** |
| Profil Teknisi | Data teknisi: nama, skill set (elektrikal, HVAC, IT, mekanikal), zona coverage, sertifikasi, dan rating performa. |
| Penugasan Cerdas | Assignment berdasarkan overlap skill tertinggi + posisi live terdekat dengan centroid cluster tiket. |
| Rute Harian Terbatch | Teknisi menerima paket kerja: peta + urutan pengerjaan + ETA + estimasi sparepart. Bisa accept/reject per batch. |
| Check-in/Check-out GPS | Verifikasi kehadiran teknisi di lokasi kerja dengan GPS check-in dan timestamp. |
| Skill-Zone Heatmap | Agregasi backlog batch per skill per zona - visualisasi kekurangan teknisi per kompetensi untuk keputusan hiring/training. |
| Hybrid Scheduling | Jam 08-16 pakai geo-batching untuk efisiensi, jam 16-08 switch ke FIFO immediate untuk respon cepat di luar jam kerja. |
| Rating & Reputasi | Skor performa teknisi berdasarkan: waktu penyelesaian vs SLA, jumlah rework, feedback user, dan jumlah tiket selesai. |

# MODULE ALERT & NOTIFIKASI

|     |     |
| --- | --- |
| **Module** | **Deskripsi** |
| Alert SIM/STNK | Monitoring masa berlaku SIM dan STNK kendaraan. Alert berjenjang: kuning (H-90 hari), merah (H-30 hari), grounded (H-0). Auto-escalate ke atasan jika diabaikan. |
| Alert Subscription | Monitoring expiry software subscription, asuransi, lease, dan kontrak vendor. Digest harian per PIC dengan dedup key. |
| Alert Service Berkala | Pengingat jatuh tempo service rutin berdasarkan odometer/jam pakai atau interval waktu. Integrasi dengan tiket maintenance. |
| Digest Harian | Semua alert di-bundle menjadi satu digest harian per PIC - mencegah alert storm yang memicu pager fatigue. |
| Channel Notifikasi | Multi-channel: Email, WhatsApp, Push Notification, In-App. Konfigurasi channel per jenis alert dan per level eskalasi. |
| Log Pengiriman | Bukti deliveri: siapa dikirim, channel apa, dibuka-tidaknya, dan timestamp. Diperlukan untuk audit compliance. |
| Eskalasi Otomatis | Jika alert tidak di-respon dalam waktu tertentu, eskalasi otomatis ke atasan langsung dan penanggung jawab aset. |

# MODULE PINJAM ASET

|     |     |
| --- | --- |
| **Module** | **Deskripsi** |
| Pengajuan Pinjam | Staff mengajukan peminjaman asset dengan detail: asset yang dipinjam, tujuan, durasi, dan bukti persetujuan atasan. |
| Self-Expiring Lease | Lease record dengan auto-reminder H-2, auto-overdue saat due_at terlewati, auto-hitung denda, dan auto-lock asset. |
| Denda Progresif | Hari 1-3 denda flat, hari 4+ naik eksponensial. Auto-potong dari deposit/saldo internal untuk efek jera. (perlu konfirmasi ke client terkait penerapan metode ini, karena jarang terjadi untuk denda progresif di peminjaman asset) |
| Grace Period & Eskalasi | Grace 4 jam tanpa denda, H+1 notifikasi ke peminjam, H+3 eskalasi ke atasan dan penanggung jawab asset. |
| Perpanjang 1-Klik | Auto-approve perpanjangan berdasarkan trust score peminjam. Jika skor buruk, butuh approval atasan. |
| Pengembalian Aset | Verifikasi kondisi aset via foto + scan QR, kalkulasi denda final, tutup lease, dan auto-unlock asset. |
| Waiting List | Sistem reservasi: jika asset sedang dipinjam, peminjam berikutnya masuk waiting list dan auto-assign saat RETURNED. |
| Reputasi Peminjam | Skor berdasarkan ketepatan waktu dan kondisi kembali - mempengaruhi limit pinjam dan durasi max berikutnya. |

# MODULE MOVEMENT

|     |     |
| --- | --- |
| **Module** | **Deskripsi** |
| Request Movement | Pengajuan perpindahan asset antar lokasi/cabang dengan alasan, estimasi tanggal, dan approval workflow. |
| Approval Chain | Multi-level approval: Supervisor, Manager, Admin Asset. Setiap approval teraudit dengan timestamp dan catatan. |
| Cross-Docking | Asset yang di-movement tidak menginap di gudang transit, langsung dialihkan ke lokasi tujuan dalam SLA 24 jam. |
| Tracking Pergerakan | Status real-time: Requested, Approved, In Transit, Received, Verified. GPS tracking untuk pengiriman jarak jauh. |
| Handover Dokumentasi | Bukti serah terima digital: foto kondisi asset saat keluar dan saat diterima, tanda tangan digital ber-hash. |
| Riwayat Movement | Full audit trail: siapa memindahkan, kapan, dari mana ke mana, kondisi asset saat perpindahan. |
| Rekonsiliasi Lokasi / Opname Asset | Verifikasi berkala: asset di database cocok dengan asset fisik di lokasi. Auto-detect mismatch untuk audit. |

# MODULE PENGHAPUSAN ASET

|     |     |
| --- | --- |
| **Module** | **Deskripsi** |
| Request Penghapusan | Pengajuan penghapusan asset afkir/rusak dengan alasan, dokumentasi kondisi, dan estimasi nilai sisa buku. |
| Dual-Approval | Persetujuan dari Manager dan Finance/BOD. Penghapusan asset bernilai tinggi butuh approval tambahan dari Direksi. |
| Dokumentasi Bukti | Foto/video kondisi asset, berita acara yang di-hash, dan inventaris komponen yang bisa di-kanibal untuk sparepart. |
| Reverse Logistics | Asset rusak ditarik via jalur retur terpisah dengan triase: refurbish, kanibal sparepart, atau disposal sesuai regulasi. |
| Soft-Delete + Quarantine | 30 hari quarantine sebelum hard-delete. Tombstone reversible - bisa dibatalkan jika ada komplain atau audit. |
| Auto-PO Reinkarnasi | Penghapusan asset kritis otomatis memicu purchase order pengganti berdasarkan kebijakan replacement ratio. (perlu konfirmasi ke client terkait penerapan metode ini, karena jarang terjadi untuk Auto PO di penghapusan asset) |
| Laporan Penghapusan | Report: asset dihapus per periode, nilai buku terakhir, gain/loss dari penghapusan, dan riwayat kanibal sparepart. |

# MODULE PURCHASE ORDER

|     |     |
| --- | --- |
| **Module** | **Deskripsi** |
| Pembuatan PO | Formulir PO: vendor, item (asset/sparepart/service), kuantitas, harga, estimasi pengiriman, dan budget code. |
| Approval PO | Workflow approval berdasarkan nilai: &lt;=5jt (Supervisor), <=50jt (Manager), &gt;50jt (Direksi). Auto-route sesuai authority matrix. |
| Three-Way Matching | Cocokkan PO, Penerimaan Barang, Invoice. Toleransi selisih harga/kuantitas yang bisa dikonfigurasi. |
| Penerimaan Barang | Checklist penerimaan: kuantitas, kondisi, serial number. Foto bukti penerimaan dan tanda tangan penerima. |
| Integrasi Vendor | Katalog vendor, histori performa delivery, dan komunikasi terkait PO via sistem. |
| Tracking Status PO | Status: Draft, Approved, Sent, Partial Received, Received, Closed. Dashboard untuk monitoring. |
| Auto-Trigger dari Sparepart | Kanban 2-Bin: bin 1 habis otomatis trigger PO, bin 2 jadi buffer tanpa perlu stock opname manual. |

# MODULE SPAREPART

|     |     |
| --- | --- |
| **Module** | **Deskripsi** |
| Master Sparepart | Database sparepart: nama, kode, spesifikasi, vendor, harga, lokasi penyimpanan, dan kompatibilitas dengan asset. |
| Stok & Warehouse | Multi-gudang dengan level stok minimum/maksimum. Visualisasi stok per lokasi dan per kategori. |
| Kanban 2-Bin | Sistem pull: bin 1 (aktif) dan bin 2 (buffer). Saat bin 1 kosong, auto-PO dan beralih ke bin 2. |
| Penggunaan per Tiket | Record sparepart yang digunakan per tiket maintenance. Auto-deduct stok dan hitung biaya per tiket. |
| Milk Run Delivery | Pengiriman sparepart terjadwal ke site secara hub-and-spoke, mengurangi pengambilan ad-hoc yang tidak efisien. |
| Prediksi Kebutuhan | Gunakan histori kerusakan untuk prediksi kebutuhan sparepart berdasarkan musim dan pola kerusakan. |
| Stock Opname | Verifikasi fisik berkala: scan barcode stok aktual vs sistem, auto-detect selisih untuk investigasi. |

# MODULE SERVICE BERKALA

|     |     |
| --- | --- |
| **Module** | **Deskripsi** |
| Jadwal Service | Penjadwalan otomatis berdasarkan interval waktu (bulanan/kuartalan/tahunan) atau odometer/jam pakai kendaraan. |
| Template Service | Template checklist per jenis asset: item yang harus diperiksa, sparepart yang perlu diganti, dan estimasi waktu. |
| Auto-Ticket Generation | Scheduler otomatis membuat tiket maintenance saat jatuh tempo service tercapai, di-assign ke teknisi yang sesuai. |
| Prefetch Sparepart | Prediksi kebutuhan sparepart berdasarkan template service dan preload ke buffer gudang sebelum jatuh tempo. |
| Checklist Digital | Teknisi mengisi checklist via mobile: centang item selesai, foto kondisi, catatan tambahan, dan tanda tangan digital. |
| Riwayat Service | Full history service per asset: kapan, oleh siapa, item yang diperiksa/diganti, biaya, dan kondisi asset. |
| Compliance Dashboard | Monitoring: asset yang sudah/w belum service, SLA compliance rate, dan biaya service per kategori asset. |

_Diagram tersedia pada berkas HTML._

Catatan: Dokumen ini dihasilkan untuk keperluan perencanaan dan pengembangan sistem. Seluruh asumsi dan kebutuhan dapat dievaluasi ulang selama fase implementasi.

Diagram interaktif tersedia pada berkas HTML ini. Untuk versi cetak (PDF), gunakan Ctrl+P di browser.

# MODULE PENJUALAN ASSET

<div class="joplin-table-wrapper"><table><tbody><tr><td><p><strong>Module</strong></p></td><td><p><strong>Deskripsi</strong></p></td></tr><tr><td><p>Request Penjualan &amp; Identifikasi Asset</p></td><td><p>Pengajuan penjualan asset &amp; Aset yang akan dijual ditentukan berdasarkan:</p><ul><li>Asset sudah tidak digunakan</li><li>Kondisi asset sudah tidak ekonomis</li><li>Asset obsolete</li><li>Kebutuhan penggantian asset</li><li>Asset memiliki nilai jual yang masih ekonomis</li></ul></td></tr><tr><td><p>Verifikasi &amp; Approval Penjualan</p></td><td><p>Multi-level approval: Supervisor, Manager, Admin Asset. Setiap approval teraudit dengan timestamp dan catatan Misalnya:</p><ul><li>Asset benar-benar milik perusahaan</li><li>Kondisi fisik sesuai</li><li>Tidak sedang digunakan</li><li>Tidak ada masalah legal/dokumen</li></ul></td></tr><tr><td><p>Penilaian Harga</p></td><td><p>Tentukan <strong>harga jual asset</strong> berdasarkan:</p><ul><li>Nilai buku</li><li>Kondisi fisik</li><li>Harga pasar</li><li>Umur aset</li><li>Hasil appraisal jika diperlukan</li></ul><p><strong>Harga jual tidak harus sama dengan nilai buku.</strong></p></td></tr><tr><td><p>Penawaran &amp; Negoisasi</p></td><td><p>Calon pembeli menerima informasi asset dan melakukan penawaran.</p><p>Sistem dapat mencatat:</p><ul><li>Nama calon pembeli</li><li>Harga penawaran</li><li>Tanggal penawaran</li><li>Hasil negosiasi</li><li>Status penawaran</li></ul></td></tr><tr><td><p>Persetujuan Harga</p></td><td><p>Setelah harga disepakati:</p><p><strong>Negotiation → Agreed → Approved. </strong>Kemudian transaksi dapat dilanjutkan (multi-level approval).</p></td></tr><tr><td><p>Riwayat Penjualan Asset</p></td><td><p>Penjualan asset kapan, dijual ke mana, kondisi asset saat dijual &amp; harga penjualan.</p></td></tr><tr><td><p>Serah Terima Penjualan Asset</p></td><td><p>Setelah pembayaran dan dokumen lengkap, dilakukan serah terima.</p><p>Dicatat:</p><ul><li>Tanggal serah terima</li><li>Penerima</li><li>Kondisi aset</li><li>Lokasi</li><li>Dokumen serah terima</li><li>Foto/bukti serah terima bila diperlukan</li></ul></td></tr><tr><td><p>Update Asset Register</p></td><td><p>Status aset berubah:</p><p><strong>ACTIVE → DISPOSAL/SALE PROCESS → SOLD</strong></p><p>Kemudian aset tidak lagi muncul sebagai aset aktif yang digunakan perusahaan.</p></td></tr></tbody></table></div>