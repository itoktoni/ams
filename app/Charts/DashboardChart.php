<?php

namespace App\Charts;

use App\Models\Aset;
use App\Models\Notification;
use App\Models\Tiket;
use App\Models\User;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardChart
{
    /**
     * User registrations over the last 7 days.
     */
    public function userRegistrations(): LarapexChart
    {
        $days = collect(range(6, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);

            return [
                'label' => $date->format('d M'),
                'count' => User::whereDate('created_at', $date)->count(),
            ];
        });

        return (new LarapexChart)->areaChart()
            ->setTitle('User Registrations')
            ->setSubtitle('New users — last 7 days')
            ->addData($days->pluck('count')->toArray())
            ->setXAxis($days->pluck('label')->toArray())
            ->setColors(['#3755c3'])
            ->setGrid()
            ->setMarkers(['#3755c3'], 4, 6);
    }

    /**
     * Notifications: read vs unread.
     */
    public function notificationStats(): LarapexChart
    {
        $read = Notification::where('read', true)->count();
        $unread = Notification::where('read', false)->count();

        return (new LarapexChart)->donutChart()
            ->setTitle('Notifications')
            ->setSubtitle('Read / Unread')
            ->addData([$read, $unread])
            ->setLabels(['Read', 'Unread'])
            ->setColors(['#16a34a', '#d97706']);
    }

    /**
     * Distribusi status aset (aktif, dipinjam, maintenance, rusak, ...).
     */
    public function asetStatusDistribution(): LarapexChart
    {
        $rows = Aset::query()
            ->select('aset_status', DB::raw('count(*) as total'))
            ->groupBy('aset_status')
            ->get();

        $labels = $rows->pluck('aset_status')->map(fn ($s) => ucfirst((string) $s))->toArray();
        $data = $rows->pluck('total')->toArray();

        if (empty($data)) {
            $labels = ['—'];
            $data = [0];
        }

        return (new LarapexChart)->donutChart()
            ->setTitle('Status Aset')
            ->setSubtitle('Distribusi kondisi aset')
            ->addData($data)
            ->setLabels($labels)
            ->setColors(['#16a34a', '#3755c3', '#d97706', '#dc2626', '#6b7280', '#9333ea']);
    }

    /**
     * Distribusi status tiket (buka, ditugaskan, progres, selesai, ...).
     */
    public function tiketStatusDistribution(): LarapexChart
    {
        $rows = Tiket::query()
            ->select('tiket_status', DB::raw('count(*) as total'))
            ->groupBy('tiket_status')
            ->get();

        $labels = $rows->pluck('tiket_status')->map(fn ($s) => ucfirst((string) $s))->toArray();
        $data = $rows->pluck('total')->toArray();

        if (empty($data)) {
            $labels = ['—'];
            $data = [0];
        }

        return (new LarapexChart)->donutChart()
            ->setTitle('Status Tiket')
            ->setSubtitle('Distribusi tiket per status')
            ->addData($data)
            ->setLabels($labels)
            ->setColors(['#3755c3', '#0ea5e9', '#d97706', '#16a34a', '#9333ea', '#dc2626']);
    }
}
