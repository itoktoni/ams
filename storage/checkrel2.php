<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$d = App\Models\DaftarTunggu::with('hasAset')->first();
echo 'has_aset=' . (isset($d->has_aset) ? 'YA' : 'NULL') . PHP_EOL;
if (isset($d->has_aset)) echo 'nama=' . $d->has_aset->aset_nama . PHP_EOL;
