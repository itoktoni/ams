<?php

namespace App\Console\Commands;

use App\Models\JadwalService;
use App\Models\TemplateService;
use App\Services\AlertService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AmsService extends Command
{
    protected $signature = 'ams:service';

    protected $description = 'Buat jadwal service dari template & picu alert service jatuh tempo';

    public function handle(): int
    {
        $alert = new AlertService;
        $alerts = $alert->cekDanBuat();

        $jadwalBaru = 0;
        foreach (TemplateService::cursor() as $template) {
            $punyaJadwal = JadwalService::where('jadwal_service_id_template', $template->template_service_id)->exists();
            if ($punyaJadwal) {
                continue;
            }

            $mulai = Carbon::today()->addDays(7);
            $jatuh = $mulai->copy();
            if ($template->template_service_interval_bulan) {
                $jatuh->addMonths((int) $template->template_service_interval_bulan);
            }

            JadwalService::create([
                'jadwal_service_id_aset' => null,
                'jadwal_service_id_template' => $template->template_service_id,
                'jadwal_service_tanggal_mulai' => $mulai,
                'jadwal_service_tanggal_jatuh_tempo' => $jatuh,
                'jadwal_service_interval_bulan' => $template->template_service_interval_bulan,
                'jadwal_service_interval_jam' => $template->template_service_interval_jam,
                'jadwal_service_status' => 'aktif',
            ]);

            $jadwalBaru++;
        }

        $this->info("Alert: {$alerts}, jadwal service dibuat: {$jadwalBaru}");

        return self::SUCCESS;
    }
}
