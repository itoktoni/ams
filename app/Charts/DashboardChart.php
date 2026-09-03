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
            ->setColors(['#16a34a', '#3755c3', '#d97706', '#dc2626', '#6b7280', '#9333ea'])
            ->setHeight(300)
            ->setToolbar(false)
            ->setOptions($this->donutOptions());
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
            ->setColors(['#3755c3', '#0ea5e9', '#d97706', '#16a34a', '#9333ea', '#dc2626'])
            ->setHeight(300)
            ->setToolbar(false)
            ->setOptions($this->donutOptions());
    }

    /**
     * Shared responsive config for donut charts so they render cleanly on mobile:
     * legend to the bottom, proportional donut, shorter height on small screens.
     * Only keys that do NOT conflict with the Larapex default options are set here
     * (chart type/height/width/toolbar come from the dedicated setters to avoid the
     * array_merge_recursive clash inside HasOptions::getOptions()).
     */
    private function donutOptions(): array
    {
        return [
            'legend' => [
                'position' => 'bottom',
                'fontSize' => '13px',
                'markers' => ['width' => 10, 'height' => 10],
                'itemMargin' => ['horizontal' => 6, 'vertical' => 2],
                'labels' => ['colors' => '#52525b'],
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '68%',
                        'labels' => [
                            'show' => true,
                            'name' => ['fontSize' => '13px'],
                            'value' => ['fontSize' => '18px', 'fontWeight' => 700],
                            'total' => [
                                'show' => true,
                                'label' => 'Total',
                                'fontSize' => '13px',
                                'fontWeight' => 600,
                                'color' => '#52525b',
                            ],
                        ],
                    ],
                ],
            ],
            // ApexCharts expects `responsive` at the top level (not under `chart`).
            'responsive' => [
                [
                    'breakpoint' => 480,
                    'options' => [
                        'chart' => ['height' => 250],
                        'legend' => ['fontSize' => '11px', 'itemMargin' => ['horizontal' => 4, 'vertical' => 1]],
                        'plotOptions' => ['pie' => ['donut' => ['size' => '62%']]],
                    ],
                ],
            ],
        ];
    }
}
