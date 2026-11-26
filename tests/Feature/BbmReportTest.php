<?php

use App\Models\BbmPengajuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::create([
        'name' => 'Admin BBM',
        'email' => 'admin-bbm@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'jenis_kendaraan' => 'mobil',
        'kendaraan' => 'B 1234 ABC',
        'verified_at' => now(),
    ]);

    $this->periode = now()->format('Y-m');
});

use App\Models\BbmKuota;

function bbmReportAjukan(User $user, string $periode, float $liter, string $status = 'menunggu'): BbmPengajuan
{
    return BbmPengajuan::create([
        'bbm_pengajuan_nomor' => 'BBM-'.uniqid(),
        'bbm_pengajuan_id_user' => $user->id,
        'bbm_pengajuan_periode' => $periode,
        'bbm_pengajuan_jenis_kendaraan' => 'mobil',
        'bbm_pengajuan_kendaraan' => 'B 1234 ABC',
        'bbm_pengajuan_liter' => $liter,
        'bbm_pengajuan_status' => $status,
        'bbm_pengajuan_tanggal' => now(),
    ]);
}

it('menampilkan form filter laporan BBM', function () {
    $this->actingAs($this->admin)->get('/laporan-bbm/table')
        ->assertOk()
        ->assertSee('Report BBM')
        ->assertSee('Periode (YYYY-MM)');
});

it('mencetak laporan BBM dengan kop dan footer yang sama seperti laporan lain', function () {
    $pengajuan = bbmReportAjukan($this->admin, $this->periode, 25);

    $html = $this->actingAs($this->admin)->get('/laporan-bbm/print')->getContent();

    expect($html)->toContain('REPORT BBM')
        ->toContain($pengajuan->bbm_pengajuan_nomor)
        ->toContain('Mobil — B 1234 ABC')
        ->toContain('Mengetahui,')
        ->toContain('Dicetak');
});

it('menampilkan ringkasan kuota per orang: kuota, terpakai, sisa', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->admin->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);
    bbmReportAjukan($this->admin, $this->periode, 60, 'disetujui');
    bbmReportAjukan($this->admin, $this->periode, 10, 'menunggu');

    $html = $this->actingAs($this->admin)->get('/laporan-bbm/print')->getContent();

    expect($html)->toContain('RINGKASAN KUOTA PER ORANG')
        ->toContain('DETAIL PENGAJUAN')
        ->toContain('Admin BBM')
        ->toContain('TOTAL');
});

it('memfilter laporan BBM berdasarkan status', function () {
    $menunggu = bbmReportAjukan($this->admin, $this->periode, 10, 'menunggu');
    $disetujui = bbmReportAjukan($this->admin, $this->periode, 20, 'disetujui');

    $html = $this->actingAs($this->admin)->get('/laporan-bbm/print?status=disetujui')->getContent();

    expect($html)->toContain($disetujui->bbm_pengajuan_nomor)
        ->not->toContain($menunggu->bbm_pengajuan_nomor);
});

it('mengekspor laporan BBM ke Excel dan PDF', function () {
    bbmReportAjukan($this->admin, $this->periode, 25);

    $excel = $this->actingAs($this->admin)->get('/laporan-bbm/export-excel');
    $excel->assertOk();
    expect($excel->headers->get('Content-Type'))->toContain('ms-excel');

    $pdf = $this->actingAs($this->admin)->get('/laporan-bbm/export-pdf');
    $pdf->assertOk();
    expect($pdf->headers->get('Content-Type'))->toContain('pdf');
});
