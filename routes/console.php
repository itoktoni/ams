<?php

use App\Console\Commands\AmsAlert;
use App\Console\Commands\AmsPenugasan;
use App\Console\Commands\AmsPenyusutan;
use App\Console\Commands\AmsService;
use App\Console\Commands\CheckPaidPayments;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(CheckPaidPayments::class)->everyMinute();

// ===== AMS scheduler =====
Schedule::command(AmsPenyusutan::class)->monthlyOn(1, '02:00');
Schedule::command(AmsAlert::class)->hourly();
Schedule::command(AmsPenugasan::class)->everyFiveMinutes();
Schedule::command(AmsService::class)->daily();
