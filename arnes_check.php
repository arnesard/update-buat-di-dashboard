<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$employees = App\Models\Employee::where('name', 'like', '%arnes%')->get();
foreach ($employees as $e) {
    echo "Name: [{$e->name}] | Plant: [{$e->plant}] | Group: [{$e->group}]\n";
}
