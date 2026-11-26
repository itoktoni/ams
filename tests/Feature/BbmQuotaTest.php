<?php

use App\Enums\Bbm\StatusPengajuanBbmEnum;
use App\Http\Controllers\BbmPengajuanController;
use App\Models\BbmKuota;
use App\Models\BbmPengajuan;
use App\Models\User;
use Database\Seeders\BbmMasterSeeder;
use Detection\MobileDetect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::create([
        'name' => 'User BBM',
        'email' => 'user-bbm@example.com',
        'password' => Hash::make('password123'),
        'role' => 'pengguna_aset',
        'jenis_kendaraan' => 'mobil',
        'kendaraan' => 'Mobil A',
        'verified_at' => now(),
    ]);

    $this->admin = User::create([
        'name' => 'Approver BBM',
        'email' => 'approver-bbm@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'jenis_kendaraan' => 'motor',
        'kendaraan' => 'Motor B',
        'verified_at' => now(),
    ]);

    $this->supervisor = User::create([
        'name' => 'Supervisor BBM',
        'email' => 'supervisor-bbm@example.com',
        'password' => Hash::make('password123'),
        'role' => 'supervisor',
        'verified_at' => now(),
    ]);

    $this->periode = now()->format('Y-m');
});

function bbmAjukan(User $user, string $periode, float $liter, string $status = 'menunggu'): BbmPengajuan
{
    return BbmPengajuan::create([
        'bbm_pengajuan_nomor' => 'BBM-'.uniqid(),
        'bbm_pengajuan_id_user' => $user->id,
        'bbm_pengajuan_periode' => $periode,
        'bbm_pengajuan_jenis_kendaraan' => 'mobil',
        'bbm_pengajuan_kendaraan' => 'Mobil A',
        'bbm_pengajuan_liter' => $liter,
        'bbm_pengajuan_status' => $status,
        'bbm_pengajuan_tanggal' => now(),
    ]);
}

it('aksi BBM ditampilkan sebagai tulisan tanpa ikon tombol', function () {
    $tabel = $this->actingAs($this->user)->get('/bbm-pengajuan/table')->getContent();
    expect($tabel)->toContain('>Create</span>')
        ->not->toContain('>add</span>')
        ->toContain('bg-primary text-on-primary');

    $form = $this->actingAs($this->user)->get('/bbm-pengajuan/create')->getContent();
    expect($form)->toContain('>Save</span>')
        ->not->toContain('>save</span>');
});

it('widget admin menampilkan pemakaian per user yang ikut berubah saat peminta diganti', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);
    $pengajuan = bbmAjukan($this->user, $this->periode, 10);

    $html = $this->actingAs($this->admin)->get('/dashboard')->getContent();
    expect(Str::after($html, e($this->user->name)))->toContain('10 L menunggu');

    $userBaru = User::create([
        'name' => 'Peminta Dashboard',
        'email' => 'peminta-dashboard@example.com',
        'password' => Hash::make('password123'),
        'role' => 'pengguna_aset',
        'jenis_kendaraan' => 'motor',
        'kendaraan' => 'Motor C 456',
        'verified_at' => now(),
    ]);
    BbmKuota::create([
        'bbm_kuota_id_user' => $userBaru->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);

    $this->actingAs($this->admin)->post('/bbm-pengajuan/update/'.$pengajuan->bbm_pengajuan_id, [
        'bbm_pengajuan_id_user' => $userBaru->id,
        'bbm_pengajuan_jenis_kendaraan' => 'motor',
        'bbm_pengajuan_kendaraan' => 'Motor C 456',
        'bbm_pengajuan_liter' => 10,
        'bbm_pengajuan_status' => 'menunggu',
    ]);

    $htmlBaru = $this->actingAs($this->admin)->get('/dashboard')->getContent();
    expect(Str::after($htmlBaru, e($userBaru->name)))->toContain('10 L menunggu')
        ->and(Str::after($htmlBaru, e($this->user->name)))->toContain('0 L menunggu');
});

it('admin bisa mengganti peminta dan kendaraan mengikuti user baru', function () {
    $pengajuan = bbmAjukan($this->user, $this->periode, 10);
    $userBaru = User::create([
        'name' => 'Peminta Baru',
        'email' => 'peminta-baru@example.com',
        'password' => Hash::make('password123'),
        'role' => 'pengguna_aset',
        'jenis_kendaraan' => 'motor',
        'kendaraan' => 'Motor B 123',
        'verified_at' => now(),
    ]);
    BbmKuota::create([
        'bbm_kuota_id_user' => $userBaru->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);

    $this->actingAs($this->admin)->post('/bbm-pengajuan/update/'.$pengajuan->bbm_pengajuan_id, [
        'bbm_pengajuan_id_user' => $userBaru->id,
        'bbm_pengajuan_jenis_kendaraan' => 'mobil', // input palsu, harus ditimpa
        'bbm_pengajuan_kendaraan' => 'Kendaraan Palsu',
        'bbm_pengajuan_liter' => 20,
        'bbm_pengajuan_status' => 'menunggu',
    ]);

    $fresh = $pengajuan->fresh();
    expect((int) $fresh->bbm_pengajuan_id_user)->toBe($userBaru->id)
        ->and($fresh->bbm_pengajuan_jenis_kendaraan)->toBe('motor')
        ->and($fresh->bbm_pengajuan_kendaraan)->toBe('Motor B 123');
});

it('user biasa tidak bisa mengganti peminta pengajuan', function () {
    $pengajuan = bbmAjukan($this->user, $this->periode, 10);

    $this->actingAs($this->user)->post('/bbm-pengajuan/update/'.$pengajuan->bbm_pengajuan_id, [
        'bbm_pengajuan_id_user' => $this->admin->id,
        'bbm_pengajuan_jenis_kendaraan' => 'mobil',
        'bbm_pengajuan_kendaraan' => 'Mobil A',
        'bbm_pengajuan_liter' => 10,
        'bbm_pengajuan_status' => 'menunggu',
    ]);

    $fresh = $pengajuan->fresh();
    expect((int) $fresh->bbm_pengajuan_id_user)->toBe($this->user->id)
        ->and($fresh->bbm_pengajuan_kendaraan)->toBe('Mobil A');
});

it('admin bisa memilih peminta dan periode saat membuat pengajuan', function () {
    $target = User::where('jenis_kendaraan', 'mobil')->where('id', '!=', $this->admin->id)->firstOrFail();
    BbmKuota::create([
        'bbm_kuota_id_user' => $target->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);

    $form = $this->actingAs($this->admin)->get('/bbm-pengajuan/create');
    $form->assertOk()->assertSee('Peminta')->assertSee('Periode');

    $bulanBerjalan = now()->format('Y-m');
    expect($form->getContent())->toMatch('/<option value="'.preg_quote($bulanBerjalan, '/').'"[^>]*selected/');

    $this->actingAs($this->admin)->post('/bbm-pengajuan/create', [
        'bbm_pengajuan_id_user' => $target->id,
        'bbm_pengajuan_periode' => $this->periode,
        'bbm_pengajuan_liter' => 20,
    ]);

    $pengajuan = BbmPengajuan::where('bbm_pengajuan_id_user', $target->id)->firstOrFail();
    expect($pengajuan->bbm_pengajuan_periode)->toBe($this->periode)
        ->and($pengajuan->bbm_pengajuan_jenis_kendaraan)->toBe('mobil');
});

it('form edit menampilkan ringkasan kuota sesuai peminta, bukan admin', function () {
    $pemilik = $this->user;
    BbmKuota::create([
        'bbm_kuota_id_user' => $pemilik->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);
    $pengajuan = bbmAjukan($this->user, $this->periode, 10);

    $html = $this->actingAs($this->admin)->get('/bbm-pengajuan/update/'.$pengajuan->bbm_pengajuan_id)->getContent();

    expect($html)->toContain('id="bbm-widget-terpakai">0 L')
        ->toContain('id="bbm-widget-menunggu">10 L');
});

it('periode urut Januari-Desember dan tabel default Januari dulu', function () {
    $tahun = (int) now()->format('Y');
    $options = BbmPengajuanController::periodeOptions($tahun);

    expect(array_keys($options))->toBe(collect(range(1, 12))->map(fn ($b) => sprintf('%04d-%02d', $tahun, $b))->all())
        ->and(array_values($options)[0])->toBe('Januari '.$tahun)
        ->and(array_values($options)[11])->toBe('Desember '.$tahun);

    $jan = bbmAjukan($this->user, sprintf('%04d-01', $tahun), 5);
    $des = bbmAjukan($this->user, sprintf('%04d-12', $tahun), 5);

    $ids = $this->actingAs($this->admin)->get('/bbm-pengajuan/table')
        ->viewData('data')->pluck('bbm_pengajuan_id')->all();

    expect(array_search($jan->bbm_pengajuan_id, $ids))->toBeLessThan(array_search($des->bbm_pengajuan_id, $ids));
});

it('seeder master COP membuat 7 user mobil dengan kuota 100 L', function () {
    $this->seed(BbmMasterSeeder::class);
    $periode = now()->format('Y-m');

    expect(User::where('unit_kode', 'COP')->count())->toBe(7)
        ->and(User::where('unit_kode', 'COP')->where('jenis_kendaraan', 'mobil')->count())->toBe(7)
        ->and(BbmKuota::where('bbm_kuota_periode', $periode)->where('bbm_kuota_liter', 100)->count())->toBe(7);
});

it('foto BBM dan struk bersifat opsional (boleh kosong)', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);

    $this->actingAs($this->user)->post('/bbm-pengajuan/create', [
        'bbm_pengajuan_liter' => 10,
    ]);

    $pengajuan = BbmPengajuan::firstOrFail();
    expect($pengajuan->bbm_pengajuan_foto_sebelum)->toBeNull()
        ->and($pengajuan->bbm_pengajuan_foto_sesudah)->toBeNull()
        ->and($pengajuan->bbm_pengajuan_foto_struk)->toBeNull();
});

it('bisa mengunggah foto sebelum, sesudah, dan struk', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);

    $file = fn () => UploadedFile::fake()->image('bbm.jpg');

    $this->actingAs($this->user)->post('/bbm-pengajuan/create', [
        'bbm_pengajuan_liter' => 10,
        'bbm_pengajuan_foto_sebelum' => $file(),
        'bbm_pengajuan_foto_sesudah' => $file(),
        'bbm_pengajuan_foto_struk' => $file(),
    ]);

    $pengajuan = BbmPengajuan::firstOrFail();
    expect($pengajuan->bbm_pengajuan_foto_sebelum)->not->toBeNull()
        ->and($pengajuan->bbm_pengajuan_foto_sesudah)->not->toBeNull()
        ->and($pengajuan->bbm_pengajuan_foto_struk)->not->toBeNull()
        ->and($pengajuan->bbm_pengajuan_foto_sebelum_url)->not->toBe('');

    // uploadFile() menulis langsung ke storage_path — verifikasi file fisik lalu bersihkan.
    foreach (['bbm_pengajuan_foto_sebelum', 'bbm_pengajuan_foto_sesudah', 'bbm_pengajuan_foto_struk'] as $field) {
        $full = storage_path('app/public/'.$pengajuan->{$field});
        expect(is_file($full))->toBeTrue();
        @unlink($full);
    }
});

it('upload foto yang tidak valid memberi pesan error yang jelas', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);

    $response = $this->actingAs($this->user)->post('/bbm-pengajuan/create', [
        'bbm_pengajuan_liter' => 10,
        'bbm_pengajuan_foto_sebelum' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
    ]);

    $response->assertRedirect();
    expect(BbmPengajuan::count())->toBe(0);
});

it('foto bisa diklik popup dan di-download dari tabel dan form', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);
    $pengajuan = bbmAjukan($this->user, $this->periode, 10);
    $pengajuan->update([
        'bbm_pengajuan_foto_sebelum' => 'bbm/sebelum.jpg',
        'bbm_pengajuan_foto_sesudah' => 'bbm/sesudah.jpg',
        'bbm_pengajuan_foto_struk' => 'bbm/struk.jpg',
    ]);

    $tabel = $this->actingAs($this->admin)->get('/bbm-pengajuan/table')->getContent();
    expect($tabel)->toContain('openImageViewer')
        ->toContain('/storage/bbm/sebelum.jpg')
        ->toContain('id="image-viewer-modal"')
        ->toContain('id="image-viewer-download"');

    $form = $this->actingAs($this->admin)->get('/bbm-pengajuan/update/'.$pengajuan->bbm_pengajuan_id)->getContent();
    expect($form)->toContain('Foto Tersimpan')
        ->toContain('openImageViewer')
        ->toContain('Download');
});

it('dashboard menampilkan widget ringkasan kuota BBM', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);
    bbmAjukan($this->user, $this->periode, 60);

    $this->actingAs($this->user)->get('/dashboard')
        ->assertOk()
        ->assertSee('Kuota BBM')
        ->assertSee('local_gas_station');
});

it('card mobile pengajuan punya checkbox select yang berfungsi', function () {
    bbmAjukan($this->user, $this->periode, 10);

    // Bind detector dengan UA mobile (singleton di-resolve saat bootstrap).
    $this->app->singleton('mobile-detect', function () {
        $detect = new MobileDetect;
        $detect->setUserAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1');

        return $detect;
    });

    $response = $this->actingAs($this->user)->get('/bbm-pengajuan/table');

    $response->assertOk()
        ->assertSee('mToggle(this)', false)
        ->assertSee('data-check', false)
        ->assertSee('Select All');
});

it('role non-admin hanya melihat pengajuan miliknya di tabel', function () {
    $milikSendiri = bbmAjukan($this->supervisor, $this->periode, 10);
    $milikOrangLain = bbmAjukan($this->user, $this->periode, 20);

    $response = $this->actingAs($this->supervisor)->get('/bbm-pengajuan/table');

    $response->assertOk()
        ->assertSee($milikSendiri->bbm_pengajuan_nomor)
        ->assertDontSee($milikOrangLain->bbm_pengajuan_nomor);
});

it('admin melihat semua pengajuan di tabel', function () {
    $milikSendiri = bbmAjukan($this->admin, $this->periode, 10);
    $milikOrangLain = bbmAjukan($this->user, $this->periode, 20);

    $response = $this->actingAs($this->admin)->get('/bbm-pengajuan/table');

    $response->assertOk()
        ->assertSee($milikSendiri->bbm_pengajuan_nomor)
        ->assertSee($milikOrangLain->bbm_pengajuan_nomor);
});

it('non-admin tidak bisa membuka form pengajuan orang lain', function () {
    $milikOrangLain = bbmAjukan($this->user, $this->periode, 20);

    $this->actingAs($this->supervisor)
        ->get('/bbm-pengajuan/update/'.$milikOrangLain->bbm_pengajuan_id)
        ->assertForbidden();
});

it('menampilkan jenis kendaraan di tabel meski nama kendaraan kosong', function () {
    $pengajuan = bbmAjukan($this->user, $this->periode, 10);
    $pengajuan->update(['bbm_pengajuan_kendaraan' => '']);

    $response = $this->actingAs($this->admin)->get('/bbm-pengajuan/table');

    $response->assertOk();
    $response->assertSee('Mobil');
});

it('hanya admin/developer yang bisa mengakses menu kuota BBM', function () {
    $this->actingAs($this->supervisor)->get('/bbm-kuota/table')->assertForbidden();
    $this->actingAs($this->user)->get('/bbm-kuota/table')->assertForbidden();
    $this->actingAs($this->admin)->get('/bbm-kuota/table')->assertOk();
});

it('hanya admin/developer yang bisa menghapus pengajuan BBM', function () {
    $pengajuan = bbmAjukan($this->user, $this->periode, 10);
    $url = '/bbm-pengajuan/delete/'.$pengajuan->bbm_pengajuan_id;

    $this->actingAs($this->supervisor)->get($url)->assertForbidden();
    expect(BbmPengajuan::count())->toBe(1);

    $this->actingAs($this->admin)->get($url)->assertRedirect();
    expect(BbmPengajuan::count())->toBe(0);
});

it('menghitung kuota terpakai dan tersedia per bulan', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);
    bbmAjukan($this->user, $this->periode, 60);

    $info = BbmPengajuan::ringkasanUser($this->user->id, $this->periode);

    expect($info['kuota'])->toBe(100.0)
        ->and($info['terpakai'])->toBe(0.0)
        ->and($info['pending'])->toBe(60.0)
        ->and($info['sisa'])->toBe(100.0)
        ->and($info['tersedia'])->toBe(40.0);
});

it('menolak pengajuan yang melebihi sisa kuota bulan ini', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);

    $this->actingAs($this->user)->post('/bbm-pengajuan/create', [
        'bbm_pengajuan_jenis_kendaraan' => 'mobil',
        'bbm_pengajuan_kendaraan' => 'Mobil A',
        'bbm_pengajuan_liter' => 150,
    ]);

    expect(BbmPengajuan::count())->toBe(0);
});

it('menampilkan kendaraan user di form pengajuan', function () {
    $response = $this->actingAs($this->user)->get('/bbm-pengajuan/create');

    $response->assertOk();
    $response->assertSee('Mobil A');
    $response->assertSee('Kendaraan (dari data user)');
});

it('mengizinkan pengajuan saat hanya jenis kendaraan yang di-set (nama opsional)', function () {
    $this->user->update(['kendaraan' => null]);
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);

    $this->actingAs($this->user)->post('/bbm-pengajuan/create', [
        'bbm_pengajuan_liter' => 10,
    ]);

    expect(BbmPengajuan::count())->toBe(1)
        ->and(BbmPengajuan::firstOrFail()->bbm_pengajuan_jenis_kendaraan)->toBe('mobil');
});

it('bisa mengisi kendaraan sendiri lewat halaman profile', function () {
    $this->actingAs($this->user)->post('/settings/profile', [
        'name' => $this->user->name,
        'email' => $this->user->email,
        'jenis_kendaraan' => 'motor',
        'kendaraan' => 'Motor B 1234 XYZ',
    ]);

    expect($this->user->fresh()->jenis_kendaraan)->toBe('motor')
        ->and($this->user->fresh()->kendaraan)->toBe('Motor B 1234 XYZ');
});

it('mengambil kendaraan otomatis dari data user (bukan dari input form)', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);

    $this->actingAs($this->user)->post('/bbm-pengajuan/create', [
        'bbm_pengajuan_jenis_kendaraan' => 'motor', // input palsu, harus diabaikan
        'bbm_pengajuan_kendaraan' => 'Motor Palsu',
        'bbm_pengajuan_liter' => 10,
    ]);

    $pengajuan = BbmPengajuan::firstOrFail();
    expect($pengajuan->bbm_pengajuan_jenis_kendaraan)->toBe('mobil')
        ->and($pengajuan->bbm_pengajuan_kendaraan)->toBe('Mobil A');
});

it('menolak pengajuan jika jenis kendaraan user belum di-set', function () {
    $this->user->update(['jenis_kendaraan' => null]);
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);

    $this->actingAs($this->user)->post('/bbm-pengajuan/create', [
        'bbm_pengajuan_jenis_kendaraan' => 'mobil',
        'bbm_pengajuan_kendaraan' => 'Mobil A',
        'bbm_pengajuan_liter' => 10,
    ]);

    expect(BbmPengajuan::count())->toBe(0);
});

it('admin/developer tidak bisa menyetujui pengajuan miliknya sendiri', function () {
    $pengajuan = bbmAjukan($this->admin, $this->periode, 10);

    $this->actingAs($this->admin)->get('/bbm-pengajuan/approve/'.$pengajuan->bbm_pengajuan_id)->assertForbidden();
    $this->actingAs($this->admin)->get('/bbm-pengajuan/reject/'.$pengajuan->bbm_pengajuan_id)->assertForbidden();

    expect($pengajuan->fresh()->bbm_pengajuan_status)->toBe('menunggu');
});

it('supervisor tidak bisa menyetujui pengajuan BBM', function () {
    $pengajuan = bbmAjukan($this->user, $this->periode, 10);

    $this->actingAs($this->supervisor)->get('/bbm-pengajuan/approve/'.$pengajuan->bbm_pengajuan_id)->assertForbidden();

    expect($pengajuan->fresh()->bbm_pengajuan_status)->toBe('menunggu');
});

it('pengajuan yang sudah disetujui tidak bisa diedit sama sekali', function () {
    $pengajuan = bbmAjukan($this->user, $this->periode, 60);
    $url = '/bbm-pengajuan/update/'.$pengajuan->bbm_pengajuan_id;
    $this->actingAs($this->admin)->get('/bbm-pengajuan/approve/'.$pengajuan->bbm_pengajuan_id);

    $this->actingAs($this->admin)->get($url)->assertRedirect();
    $this->actingAs($this->admin)->post($url, [
        'bbm_pengajuan_jenis_kendaraan' => 'mobil',
        'bbm_pengajuan_kendaraan' => 'Mobil A',
        'bbm_pengajuan_liter' => 999,
        'bbm_pengajuan_catatan' => 'coba diubah',
        'bbm_pengajuan_status' => 'disetujui',
    ]);

    $fresh = $pengajuan->fresh();
    expect((float) $fresh->bbm_pengajuan_liter)->toBe(60.0)
        ->and($fresh->bbm_pengajuan_catatan)->toBeNull();

    $html = $this->actingAs($this->admin)->get('/bbm-pengajuan/table')->getContent();
    expect($html)->not->toContain('>edit</span>');
});

it('jumlah BBM tidak bisa diubah setelah pengajuan disetujui', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);
    $pengajuan = bbmAjukan($this->user, $this->periode, 60);
    $this->actingAs($this->admin)->get('/bbm-pengajuan/approve/'.$pengajuan->bbm_pengajuan_id);

    $this->actingAs($this->admin)->post('/bbm-pengajuan/update/'.$pengajuan->bbm_pengajuan_id, [
        'bbm_pengajuan_jenis_kendaraan' => 'mobil',
        'bbm_pengajuan_kendaraan' => 'Mobil A',
        'bbm_pengajuan_liter' => 999,
        'bbm_pengajuan_status' => 'disetujui',
    ]);

    expect((float) $pengajuan->fresh()->bbm_pengajuan_liter)->toBe(60.0);
});

it('memindahkan liter ke terpakai setelah pengajuan disetujui', function () {
    BbmKuota::create([
        'bbm_kuota_id_user' => $this->user->id,
        'bbm_kuota_periode' => $this->periode,
        'bbm_kuota_liter' => 100,
    ]);
    $pengajuan = bbmAjukan($this->user, $this->periode, 60);

    $this->actingAs($this->admin)->get('/bbm-pengajuan/approve/'.$pengajuan->bbm_pengajuan_id);

    $info = BbmPengajuan::ringkasanUser($this->user->id, $this->periode);
    expect($pengajuan->fresh()->bbm_pengajuan_status)->toBe(StatusPengajuanBbmEnum::DISETUJUI)
        ->and($info['terpakai'])->toBe(60.0)
        ->and($info['pending'])->toBe(0.0)
        ->and($info['tersedia'])->toBe(40.0);
});
