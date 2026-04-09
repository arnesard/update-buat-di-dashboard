<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    Illuminate\Http\Request::capture()
);

use App\Models\Reception;

echo "Finding top 5 employees with most records in each plant:\n\n";

$topEmployees = Reception::join('employees', 'receptions.employee_id', '=', 'employees.employee_id')
    ->selectRaw('employees.name, employees.plant, COUNT(*) as record_count')
    ->groupBy('employees.name', 'employees.plant')
    ->orderBy('record_count', 'desc')
    ->limit(20)
    ->get();

foreach ($topEmployees as $emp) {
    echo "Plant: {$emp->plant} | Operator: {$emp->name} | Records: {$emp->record_count}\n";
}
