<?php

namespace Database\Seeders;

use App\Models\Aset;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

// Perbaiki brand/model generik "Brand"/"MOD-x" jadi merek realistis
// perusahaan IT, + sebar tanggal perolehan Jan 2024 s/d bulan ini (Nov 2026).
// Idempoten: updateOrCreate by aset_kode, aman dijalankan ulang.
class FixAsetBrandSeeder extends Seeder
{
    public function run(): void
    {
        // [kode => [merek, model]] — null merek untuk tanah/gedung
        $map = [
            'A-001' => ['Toyota', 'Innova Zenix 2.0 Q HV'],
            'A-002' => ['Dell', 'PowerEdge R740'],
            'A-003' => ['Philips', 'Efficia CM120'],
            'A-004' => ['Honda', 'EU70is'],
            'A-005' => ['HP', 'Z2 Tower G9'],
            'A-006' => ['Toyota', 'Hiace Commuter Ambulance'],
            'A-007' => ['B. Braun', 'Infusomat Space'],
            'A-008' => ['Cisco', 'Catalyst C9300-24T'],
            'A-009' => ['Atlas Copco', 'GA15 VSD'],
            'A-010' => ['Philips', 'IntelliVue MX450'],
            'A-101' => [null, 'SHM-001 / 500m2'],
            'A-102' => [null, 'IMB-220 / 3 lantai'],
            'A-103' => ['Autodesk', 'AutoCAD 2024'],
            'A-104' => ['Microsoft', 'Office 365 E3'],
            'A-105' => ['HP', 'Z2 Tower G9 i7'],
            'A-106' => ['Lenovo', 'ThinkPad T14 Gen 4'],
            'A-107' => ['Honda', 'Vario 160 CBS'],
            'A-108' => ['Toyota', 'Fortuner 2.8 VRZ'],
            'CF-001' => [null, 'SHM-002 / 750m2'],
            'CF-002' => [null, 'SHM-003 / 2000m2'],
            'CF-003' => [null, 'IMB-300 / pabrik'],
            'CF-004' => [null, 'IMB-301 / gudang'],
            'CF-005' => ['Toyota', 'Avanza 1.5 G CVT'],
            'CF-006' => ['Mitsubishi', 'L300 EU4'],
            'CF-007' => ['Honda', 'Beat Deluxe'],
            'CF-008' => ['Yamaha', 'NMAX 155 Connected'],
            'CF-009' => ['Dell', 'PowerEdge R450'],
            'CF-010' => ['Lenovo', 'ThinkCentre M70s i3'],
            'CF-011' => ['Dell', 'Latitude 5440 i7'],
            'CF-012' => ['Apple', 'MacBook Pro 14 M3'],
            'CF-013' => ['Microsoft', 'Windows 11 Pro OLP'],
            'CF-014' => ['Microsoft', 'SQL Server 2022 Std'],
            'CF-015' => ['Autodesk', 'AutoCAD 2024'],
            'CF-016' => ['Adobe', 'Creative Cloud All Apps'],
            'CF-017' => ['Haas', 'VF-2SS'],
            'CF-018' => ['Honda', 'EU70is'],
            'CF-019' => ['Daikin', 'FTKC35UVM4 1.5PK'],
            'CF-020' => ['Hikvision', 'DS-2CD2T46G2 4MP'],
            'CF-021' => ['Informa', 'Meja Kerja Odete'],
            'CF-022' => ['Informa', 'Kursi Kantor Kenji'],
            'CF-023' => ['Philips', 'Efficia CM120'],
            'CF-024' => ['B. Braun', 'Infusomat Space'],
        ];

        // Sebar tanggal perolehan Jan 2024 -> Nov 2026 (35 bulan, sampai bulan ini)
        $start = Carbon::create(2024, 1, 1)->startOfMonth();
        $i = 0;
        foreach ($map as $kode => [$merek, $model]) {
            $aset = Aset::where('aset_kode', $kode)->first();
            if (! $aset) {
                continue;
            }
            $tgl = (clone $start)->addMonths($i % 35)->day(rand(1, 28));
            // Jangan lewati bulan berjalan
            if ($tgl->isFuture()) {
                $tgl = Carbon::create(2026, 11, rand(1, 4));
            }
            $aset->update([
                'aset_merek' => $merek,
                'aset_model' => $model,
                'aset_tanggal_perolehan' => $tgl->copy()->startOfDay(),
                'aset_tanggal_mulai_susut' => $tgl->copy()->startOfDay(),
            ]);
            $i++;
        }

        $this->command->info('FixAsetBrandSeeder: '.$i.' aset diperbaiki (brand realistis, tgl sebar Jan 2024–Nov 2026).');
    }
}
