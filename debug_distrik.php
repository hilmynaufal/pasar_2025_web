<?php
// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

// Fetch unique distrik names
$distriks = DB::table('transaksi')
    ->select('nama_distrik')
    ->distinct()
    ->orderBy('nama_distrik')
    ->pluck('nama_distrik')
    ->toArray();

$output = "Total Distrik: " . count($distriks) . "\n";
$output .= "List:\n";
foreach ($distriks as $d) {
    $hex = bin2hex($d);
    $output .= "['$d'] (Hex: $hex)\n";
}

file_put_contents(__DIR__ . '/distrik_dump.txt', $output);

echo "Dump created at " . __DIR__ . '/distrik_dump.txt';
