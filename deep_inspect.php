<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DEEP DATA INSPECTION ---\n\n";

// 1. Check for employee_id mismatch in Receptions
echo "1. Receptions Employee ID Hex check (First 5):\n";
$receptions = App\Models\Reception::take(5)->get();
foreach ($receptions as $r) {
    $id = $r->employee_id;
    echo "ID: " . $r->id . " | Raw: [" . $id . "] | Hex: " . bin2hex($id) . "\n";
}

echo "\n2. Employees Employee ID Hex check (First 5):\n";
$employees = App\Models\Employee::take(5)->get();
foreach ($employees as $e) {
    $id = $e->employee_id;
    echo "ID: " . $e->id . " | Name: [" . $e->name . "] | Raw: [" . $id . "] | Hex: " . bin2hex($id) . "\n";
}

echo "\n3. Join Discrepancy Test:\n";
$joined = DB::select("
    SELECT r.id, r.employee_id as rec_eid, e.employee_id as emp_eid, e.name
    FROM receptions r
    LEFT JOIN employees e ON r.employee_id = e.employee_id
    LIMIT 5
");
foreach ($joined as $j) {
    echo "RecID: " . $j->id . " | Rec EID: [" . $j->rec_eid . "] | Emp EID: [" . ($j->emp_eid ?? 'MISSING') . "] | Name: [" . ($j->name ?? 'MISSING') . "]\n";
}

echo "\n4. SQL Query Simulation (Search: arnes):\n";
$term = 'arnes';
$results = DB::select("
    SELECT count(*) as total
    FROM receptions
    LEFT JOIN employees ON receptions.employee_id = employees.employee_id
    WHERE LOWER(employees.name) LIKE ?
", ["%".strtolower($term)."%"]);
echo "Results for 'arnes': " . $results[0]->total . "\n";

echo "\n--- INSPECTION END ---\n";
