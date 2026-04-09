<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DATA AUDIT: EMPLOYEE vs RECEPTIONS ---\n\n";

$employees = App\Models\Employee::all();
$total_matching = 0;
$total_receptions = App\Models\Reception::count();

foreach ($employees as $e) {
    $count = App\Models\Reception::where('employee_id', $e->employee_id)->count();
    if ($count > 0) {
        echo "MATCH: [{$e->name}] (ID: {$e->employee_id}) -> {$count} records\n";
        $total_matching += $count;
    } else {
        // echo "NO DATA: [{$e->name}] (ID: {$e->employee_id})\n";
    }
}

echo "\nTotal Receptions: " . $total_receptions . "\n";
echo "Total Linked to Employees: " . $total_matching . "\n";

if ($total_receptions > $total_matching) {
    echo "WARNING: " . ($total_receptions - $total_matching) . " receptions are orphaned (ID mismatch)!\n";
    $orphans = App\Models\Reception::whereNotIn('employee_id', $employees->pluck('employee_id'))->get();
    foreach ($orphans as $o) {
        echo "Orphan ID: " . $o->id . " | Emp ID in Rec: [" . $o->employee_id . "]\n";
    }
}

echo "\n--- AUDIT END ---\n";
