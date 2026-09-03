<?php

namespace Modules\Cms\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Cms\Models\Content;
use Modules\Cms\Models\Field;
use Modules\Cms\Models\Menu;
use Modules\Cms\Models\Section;
use Modules\Cms\Models\Type;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        $type = Type::updateOrCreate(
            ['slug' => 'homepage'],
            [
                'name' => 'Homepage',
                'type' => 'custom',
                'description' => 'KIRO AMS — hero, tentang, fitur utama (registrasi/penyusutan/tiket/pinjam/movement/sparepart), mitra, CTA.',
                'supports' => ['title', 'slug'],
                'is_active' => true,
            ]
        );

        $fields = $this->seedFields($type);
        $sections = $this->seedSections($type, $fields);
        $this->seedContent($type, $sections);
        $this->seedMenus();
    }

    private function seedFields(Type $type): array
    {
        $defs = [
            ['name' => 'hero', 'label' => 'Hero', 'type' => 'container', 'mode' => 'multiple', 'sort_order' => 10, 'children' => [
                ['name' => 'eyebrow', 'label' => 'Eyebrow', 'type' => 'text', 'sort_order' => 1],
                ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'sort_order' => 2],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'sort_order' => 3],
                ['name' => 'image', 'label' => 'Gambar', 'type' => 'image', 'sort_order' => 4],
                ['name' => 'cta_text', 'label' => 'Tombol 1 Teks', 'type' => 'text', 'sort_order' => 5],
                ['name' => 'cta_link', 'label' => 'Tombol 1 Link', 'type' => 'url', 'sort_order' => 6],
                ['name' => 'cta2_text', 'label' => 'Tombol 2 Teks', 'type' => 'text', 'sort_order' => 7],
                ['name' => 'cta2_link', 'label' => 'Tombol 2 Link', 'type' => 'url', 'sort_order' => 8],
            ]],
            ['name' => 'about', 'label' => 'Tentang Kami', 'type' => 'container', 'mode' => 'single', 'sort_order' => 20, 'children' => [
                ['name' => 'subtitle', 'label' => 'Subjudul', 'type' => 'text', 'sort_order' => 1],
                ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'sort_order' => 2],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'sort_order' => 3],
                ['name' => 'image', 'label' => 'Gambar', 'type' => 'image', 'sort_order' => 4],
                ['name' => 'cta_text', 'label' => 'Tombol Teks', 'type' => 'text', 'sort_order' => 5],
                ['name' => 'cta_link', 'label' => 'Tombol Link', 'type' => 'url', 'sort_order' => 6],
            ]],
            ['name' => 'services', 'label' => 'Fitur Utama', 'type' => 'container', 'mode' => 'multiple', 'sort_order' => 30, 'children' => [
                ['name' => 'icon', 'label' => 'Icon (Material Symbols)', 'type' => 'text', 'sort_order' => 1],
                ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'sort_order' => 2],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'sort_order' => 3],
            ]],
            ['name' => 'clients', 'label' => 'Mitra & Cabang', 'type' => 'container', 'mode' => 'multiple', 'sort_order' => 40, 'children' => [
                ['name' => 'name', 'label' => 'Nama', 'type' => 'text', 'sort_order' => 1],
                ['name' => 'logo', 'label' => 'Logo', 'type' => 'image', 'sort_order' => 2],
            ]],
            ['name' => 'cta', 'label' => 'CTA Penutup', 'type' => 'container', 'mode' => 'single', 'sort_order' => 50, 'children' => [
                ['name' => 'title', 'label' => 'Judul', 'type' => 'text', 'sort_order' => 1],
                ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'sort_order' => 2],
                ['name' => 'button1_text', 'label' => 'Tombol 1', 'type' => 'text', 'sort_order' => 3],
                ['name' => 'button1_link', 'label' => 'Link 1', 'type' => 'url', 'sort_order' => 4],
                ['name' => 'button2_text', 'label' => 'Tombol 2', 'type' => 'text', 'sort_order' => 5],
                ['name' => 'button2_link', 'label' => 'Link 2', 'type' => 'url', 'sort_order' => 6],
            ]],
        ];

        $byName = [];
        foreach ($defs as $def) {
            $children = $def['children'] ?? [];
            unset($def['children']);
            $def['type_id'] = $type->id;

            $parent = Field::updateOrCreate(['name' => $def['name']], $def);
            $byName[$def['name']] = $parent;

            foreach ($children as $child) {
                Field::updateOrCreate(
                    ['name' => $child['name'], 'parent_id' => $parent->id],
                    array_merge($child, ['parent_id' => $parent->id, 'type_id' => $type->id])
                );
            }
        }

        return $byName;
    }

    private function seedSections(Type $type, array $fields): array
    {
        $order = [
            'hero' => 10,
            'about' => 20,
            'services' => 30,
            'clients' => 40,
            'cta' => 50,
        ];

        $sections = [];
        foreach ($order as $name => $sort) {
            $field = $fields[$name] ?? null;
            if (! $field) continue;

            $sections[$name] = Section::updateOrCreate(
                ['name' => $name],
                [
                    'description' => match ($name) {
                        'services' => 'Fitur Utama — registrasi, penyusutan, tiket, pinjam, movement, sparepart',
                        'hero' => 'Hero — KIRO Asset Management',
                        'about' => 'Tentang — KIRO AMS',
                        'clients' => 'Mitra & Cabang — KIRO AMS',
                        'cta' => 'CTA Penutup — KIRO AMS',
                        default => ucfirst($name) . ' — KIRO AMS',
                    },
                    'icon' => match ($name) { 'hero' => 'home', 'about' => 'info', 'services' => 'inventory_2', 'clients' => 'handshake', 'cta' => 'dashboard', default => 'layers' },
                    'content_type_id' => $type->id,
                    'field_ids' => [$field->id],
                    'sort_order' => $sort,
                    'is_active' => true,
                ]
            );
        }

        return $sections;
    }

    private function seedContent(Type $type, array $sections): void
    {
        $meta = [
            'hero' => [
                [
                    'eyebrow' => 'KIRO Asset Management',
                    'title' => 'Kelola Siklus Hidup Aset — dari Pengadaan hingga Penghapusan',
                    'description' => 'Platform terpadu untuk registrasi, penyusutan, peminjaman, pergerakan, tiket & maintenance, alert dokumen, sparepart, dan lelang aset. Visibilitas real-time di seluruh cabang, teraudit & siap compliance.',
                    'image' => '',
                    'cta_text' => 'Masuk Dashboard',
                    'cta_link' => '/login',
                    'cta2_text' => 'Lihat Lelang Aset',
                    'cta2_link' => '/lelang',
                ],
            ],
            'about' => [
                'subtitle' => 'Tentang KIRO AMS',
                'title' => 'Aset terpantau, biaya terkendali, audit siap',
                'description' => "KIRO AMS menggantikan spreadsheet manual dengan sistem terpusat yang melacak ribuan aset di seluruh cabang — kendaraan, elektronik, furniture, hingga infrastruktur TI.\n\nOtomasi penyusutan garis lurus, SLA tiket dengan geo-batching, alert SIM/STNK & subscription, hingga kanban sparepart 2-Bin — semua teraudit dengan hash-chain ledger dan dashboard real-time.",
                'image' => '',
                'cta_text' => 'Hubungi Kami',
                'cta_link' => '/contact',
            ],
            'services' => [
                ['icon' => 'inventory_2', 'title' => 'Registrasi & Inventaris', 'description' => 'Database sentral seluruh aset: QR/Barcode unik, kategori & klasifikasi, status lifecycle (Aktif/Dipinjam/Maintenance/Rusak/Dihapus), dan dokumen digital.'],
                ['icon' => 'calculate', 'title' => 'Penyusutan Otomatis', 'description' => 'Garis lurus dengan ledger append-only & hash chain. Laporan bulanan/tahunan, simulasi what-if, dan rekonsiliasi siap audit.'],
                ['icon' => 'build', 'title' => 'Tiket & Maintenance', 'description' => 'SLA berjenjang, geo-batching teknisi, fast-lane Critical, check-in GPS + foto before/after, dan analytics MTTR.'],
                ['icon' => 'swap_horiz', 'title' => 'Pinjam & Movement', 'description' => 'Self-expiring lease, denda progresif, waiting list, serta tracking pergerakan antar lokasi dengan approval chain & handover digital.'],
                ['icon' => 'notifications_active', 'title' => 'Alert & Notifikasi', 'description' => 'Alert SIM/STNK/subscription/service berkala, digest harian anti-spam, multi-channel (Email/WA/Push) & eskalasi otomatis.'],
                ['icon' => 'handyman', 'title' => 'Sparepart & Penghapusan', 'description' => 'Kanban 2-Bin, milk-run, prediksi kebutuhan, serta penghapusan dual-approval dengan quarantine 30 hari & reverse logistics.'],
            ],
            'clients' => [
                ['name' => 'Kantor Pusat', 'logo' => ''],
                ['name' => 'Cabang Regional', 'logo' => ''],
                ['name' => 'Gudang & Logistik', 'logo' => ''],
                ['name' => 'Mitra Vendor & Service', 'logo' => ''],
            ],
            'cta' => [
                'title' => 'Butuh visibilitas aset real-time?',
                'description' => 'Masuk ke dashboard untuk kelola aset, tiket, dan lelang dalam satu tempat. Seluruh siklus hidup teraudit dan siap compliance.',
                'button1_text' => 'Masuk Dashboard',
                'button1_link' => '/login',
                'button2_text' => 'Lihat Lelang',
                'button2_link' => '/lelang',
            ],
        ];

        $content = Content::updateOrCreate(
            ['slug' => 'homepage'],
            [
                'title' => 'Homepage',
                'content' => null,
                'excerpt' => 'KIRO Asset Management — homepage',
                'status' => 'published',
                'published_at' => now(),
                'content_type_id' => $type->id,
                'meta' => $meta,
                'active_sections' => array_values(array_map(fn ($s) => $s->id, $sections)),
            ]
        );

        if (empty($content->slug)) {
            $content->slug = Str::slug($content->title) . '-' . $content->id;
            $content->saveQuietly();
        }
    }

    private function seedMenus(): void
    {
        Menu::updateOrCreate(
            ['slug' => 'main-menu'],
            [
                'name' => 'Main Navigation',
                'location' => 'main',
                'is_active' => true,
                'sort_order' => 0,
                'items' => [
                    ['label' => 'Beranda', 'url' => '/', 'sort_order' => 0],
                    ['label' => 'Fitur', 'url' => '/#produk', 'sort_order' => 1],
                    ['label' => 'Tentang', 'url' => '/#tentang', 'sort_order' => 2],
                    ['label' => 'Lelang', 'url' => '/lelang', 'sort_order' => 3],
                    ['label' => 'Kontak', 'url' => '/contact', 'sort_order' => 4],
                ],
            ]
        );

        Menu::updateOrCreate(
            ['slug' => 'footer-menu'],
            [
                'name' => 'Footer',
                'location' => 'footer',
                'is_active' => true,
                'sort_order' => 0,
                'items' => [
                    ['label' => 'Tautan', 'sort_order' => 0, 'children' => [
                        ['label' => 'Fitur', 'url' => '/#produk', 'sort_order' => 0],
                        ['label' => 'Tentang', 'url' => '/#tentang', 'sort_order' => 1],
                        ['label' => 'Lelang', 'url' => '/lelang', 'sort_order' => 2],
                        ['label' => 'Kontak', 'url' => '/contact', 'sort_order' => 3],
                    ]],
                    ['label' => 'Fitur', 'sort_order' => 1, 'children' => [
                        ['label' => 'Registrasi Aset', 'url' => '/#produk', 'sort_order' => 0],
                        ['label' => 'Penyusutan', 'url' => '/#produk', 'sort_order' => 1],
                        ['label' => 'Tiket & Maintenance', 'url' => '/#produk', 'sort_order' => 2],
                        ['label' => 'Pinjam & Movement', 'url' => '/#produk', 'sort_order' => 3],
                        ['label' => 'Alert & Sparepart', 'url' => '/#produk', 'sort_order' => 4],
                        ['label' => 'Lelang Aset', 'url' => '/lelang', 'sort_order' => 5],
                    ]],
                ],
            ]
        );
        Menu::whereIn('slug', ['main-navigation', 'footer-company'])->delete();
    }
}
