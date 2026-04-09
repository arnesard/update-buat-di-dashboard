<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- FILTER SIMULATION AUDIT ---\n\n";

function runSimpleQuery($name, $plant = null, $group = null) {
    echo "Simulating Filter: Name=[$name], Plant=[$plant], Group=[$group]\n";
    $query = App\Models\Reception::leftJoin('employees', 'receptions.employee_id', '=', 'employees.employee_id')
        ->select('receptions.*', 'employees.name as emp_name', 'employees.plant as emp_plant', 'employees.group as emp_group');
    
    if ($plant) $query->where('employees.plant', $plant);
    if ($group) $query->where('employees.group', $group);
    if ($name) {
        $term = '%' . strtolower($name) . '%';
        $query->where(function($q) use ($term) {
            $q->whereRaw('LOWER(employees.name) LIKE ?', [$term])
              ->orWhereRaw('LOWER(receptions.employee_id) LIKE ?', [$term]);
        });
    }

    $count = $query->count();
    echo "Count: $count\n";
    if ($count > 0) {
        $first = $query->first();
        echo "Example Result: " . $first->emp_name . " (ID: " . $first->employee_id . ") in Plant " . $first->emp_plant . "\n";
    }
    echo "-----------------------------------\n";
}

runSimpleQuery('arnes', 'B');
runSimpleQuery(null, 'B');
runSimpleQuery('perdi', null);
runSimpleQuery('paijo', null);

echo "\n--- AUDIT END ---\n";
