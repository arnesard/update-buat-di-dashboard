<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reception;
use App\Models\Employee;
use App\Exports\ProductionExport;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProductionController extends Controller
{
    public function dashboard(Request $request)
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $operatorName = $request->get('operator_name');

        // --- JOB TYPE FILTER PER PLANT (for Individual Trend Charts) ---
        $sevenDaysAgo = Carbon::today()->subDays(6);
        $plants = ['B', 'H', 'I', 'T'];

        // Build 7-day date labels
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = Carbon::today()->subDays(6 - $i)->format('Y-m-d');
        }
        $trendDates = collect($dates)->map(fn($d) => date('d M', strtotime($d)));

        // Cache key based on today's date + selected jobs
        $jobParams = [];
        foreach ($plants as $p) {
            $jobParams[] = $request->get('job_' . strtolower($p), '');
        }
        $trendCacheKey = 'dashboard_trends_' . $today->format('Y-m-d') . '_' . md5(implode('|', $jobParams));

        // Cache trend data for 60 seconds (heavy queries with JOINs)
        $trendResult = Cache::remember($trendCacheKey, 60, function () use ($plants, $request, $sevenDaysAgo, $dates) {
            $jobTypesPerPlant = [];
            $selectedJobPerPlant = [];
            $trendSeriesPerPlant = [];

            foreach ($plants as $p) {
                $paramKey = 'job_' . strtolower($p);

                // Get distinct job_today values for this plant with count
                $jobCounts = Reception::join('employees', 'receptions.employee_id', '=', 'employees.employee_id')
                    ->where('employees.plant', $p)
                    ->where('receptions.date', '>=', $sevenDaysAgo)
                    ->whereNotNull('receptions.job_today')
                    ->where('receptions.job_today', '!=', '')
                    ->selectRaw('receptions.job_today, COUNT(*) as cnt')
                    ->groupBy('receptions.job_today')
                    ->orderByDesc('cnt')
                    ->get();

                $jobs = $jobCounts->pluck('job_today')->values();
                $mostPopularJob = $jobCounts->first()?->job_today;

                $jobTypesPerPlant[$p] = $jobs;

                // Selected job (from request, or most popular)
                $selectedJob = $request->get($paramKey, $mostPopularJob);
                $selectedJobPerPlant[$p] = $selectedJob;

                // Get all employees who did this job in this plant in last 7 days
                $empData = Reception::join('employees', 'receptions.employee_id', '=', 'employees.employee_id')
                    ->where('employees.plant', $p)
                    ->where('receptions.job_today', $selectedJob)
                    ->where('receptions.date', '>=', $sevenDaysAgo)
                    ->selectRaw('employees.name, receptions.date, SUM(receptions.production_count) as total')
                    ->groupBy('employees.name', 'receptions.date')
                    ->orderBy('employees.name')
                    ->get();

                // Build series
                $series = [];
                foreach ($empData as $row) {
                    $dateKey = is_string($row->date) ? substr($row->date, 0, 10) : $row->date->format('Y-m-d');
                    $series[$row->name][$dateKey] = $row->total;
                }

                // Normalize to fill all 7 days
                $normalized = [];
                foreach ($series as $name => $dayData) {
                    $normalized[$name] = collect($dates)->map(fn($d) => $dayData[$d] ?? 0)->toArray();
                }

                $trendSeriesPerPlant[$p] = $normalized;
            }

            return compact('jobTypesPerPlant', 'selectedJobPerPlant', 'trendSeriesPerPlant');
        });

        $jobTypesPerPlant = $trendResult['jobTypesPerPlant'];
        $selectedJobPerPlant = $trendResult['selectedJobPerPlant'];
        $trendSeriesPerPlant = $trendResult['trendSeriesPerPlant'];

        // 1. Ambil Data Detail untuk Tabel (Join Employee) — only today's data
        $receptionsQuery = Reception::leftJoin('employees', 'receptions.employee_id', '=', 'employees.employee_id')
            ->select(
                'receptions.id',
                'receptions.shift',
                'receptions.ritase_result',
                'receptions.production_count',
                'receptions.notes',
                'receptions.date',
                'receptions.created_at',
                'employees.plant',
                'employees.name as operator_name',
                'employees.group as group',
                'employees.primary_job_type as job_type',
                'employees.default_status as status'
            )
            ->whereDate('receptions.date', $today)
            ->orderBy('receptions.created_at', 'desc');

        if ($operatorName) {
            $receptionsQuery->where('employees.name', 'like', '%' . $operatorName . '%');
        }

        $receptions = $receptionsQuery->get();

        // 2. Statistik Card — COMBINED into 1 query instead of 3 separate queries
        $todayStats = Reception::whereDate('date', $today)
            ->selectRaw('COALESCE(SUM(production_count), 0) as total_production, COALESCE(SUM(ritase_result), 0) as total_ritase, COUNT(DISTINCT employee_id) as total_employees')
            ->first();

        $totalProduction = $todayStats->total_production;
        $totalRitase = $todayStats->total_ritase;
        $totalEmployees = $todayStats->total_employees;

        // 3. Job Stats for today
        $jobStats = Reception::leftJoin('employees', 'receptions.employee_id', '=', 'employees.employee_id')
            ->selectRaw('employees.primary_job_type as job, SUM(receptions.production_count) as total')
            ->whereDate('receptions.date', $today)
            ->whereNotNull('employees.primary_job_type')
            ->groupBy('employees.primary_job_type')
            ->get();

        $currentHour = $now->hour;
        $currentShift = $this->getCurrentShift($currentHour);

        // 4. SINGLE query for plant+group data (used for both monthly chart AND daily chart)
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $allPlantData = Reception::join('employees', 'receptions.employee_id', '=', 'employees.employee_id')
            ->selectRaw('employees.plant, employees.group, SUM(receptions.production_count) as total')
            ->whereMonth('receptions.date', $currentMonth)
            ->whereYear('receptions.date', $currentYear)
            ->groupBy('employees.plant', 'employees.group')
            ->get();

        $dataPlantB = $allPlantData->where('plant', 'B')->pluck('total', 'group')->sortKeys();
        $dataPlantH = $allPlantData->where('plant', 'H')->pluck('total', 'group')->sortKeys();
        $dataPlantI = $allPlantData->where('plant', 'I')->pluck('total', 'group')->sortKeys();
        $dataPlantT = $allPlantData->where('plant', 'T')->pluck('total', 'group')->sortKeys();

        // 5. Use same allPlantData filtered for today (reuse from receptions already loaded)
        $plantData = $receptions->groupBy('plant')->map(function ($group) {
            return $group->groupBy('group')->map(function ($subgroup) {
                return (object)[
                    'plant' => $subgroup->first()->plant,
                    'group' => $subgroup->first()->group,
                    'total' => $subgroup->sum('production_count'),
                ];
            })->values();
        })->flatten();

        // 6. All distinct job types (from receptions + employees)
        $jobFromReceptions = Reception::whereNotNull('job_today')
            ->where('job_today', '!=', '')
            ->distinct()
            ->pluck('job_today');

        $jobFromEmployees = Employee::whereNotNull('primary_job_type')
            ->where('primary_job_type', '!=', '')
            ->distinct()
            ->pluck('primary_job_type');

        $allJobTypes = $jobFromReceptions->merge($jobFromEmployees)
            ->unique()
            ->sort()
            ->values();

        // 7. Trend 7 Days (cached 60s)
        // Generate all 7 dates to ensure chart always has 7 data points
        $last7Dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $last7Dates[] = Carbon::today()->subDays($i)->format('Y-m-d');
        }

        $data7Days = Cache::remember('dashboard_7days_' . $today->format('Y-m-d'), 60, function () use ($last7Dates) {
            $sevenDaysAgo = Carbon::today()->subDays(6);

            // Query only the last 7 days
            $rawData = Reception::selectRaw('DATE(date) as date, SUM(production_count) as total')
                ->where('date', '>=', $sevenDaysAgo)
                ->groupBy(DB::raw('DATE(date)'))
                ->orderBy('date', 'asc')
                ->get()
                ->keyBy(function ($item) {
                    return is_string($item->date) ? substr($item->date, 0, 10) : $item->date->format('Y-m-d');
                });

            // Fill in missing dates with 0 and cast values to int
            return collect($last7Dates)->map(function ($d) use ($rawData) {
                return (object) [
                    'date'  => $d,
                    'total' => (int) ($rawData[$d]->total ?? 0),
                ];
            });
        });

        return view('dashboard_auth', compact(
            'receptions',
            'dataPlantB',
            'dataPlantH',
            'dataPlantI',
            'dataPlantT',
            'jobTypesPerPlant',
            'selectedJobPerPlant',
            'trendSeriesPerPlant',
            'trendDates',
            'allJobTypes',
            'totalProduction',
            'totalEmployees',
            'totalRitase',
            'currentShift',
            'data7Days',
            'plantData',
            'jobStats',
            'operatorName'
        ));
    }

    /**
     * AJAX endpoint: return trend data for a specific plant + job_today
     */
    public function trendData(Request $request)
    {
        $plant = $request->get('plant');
        $job = $request->get('job');

        $sevenDaysAgo = Carbon::today()->subDays(6);
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = Carbon::today()->subDays(6 - $i)->format('Y-m-d');
        }

        $empData = Reception::join('employees', 'receptions.employee_id', '=', 'employees.employee_id')
            ->where('employees.plant', $plant)
            ->where('receptions.job_today', $job)
            ->where('receptions.date', '>=', $sevenDaysAgo)
            ->selectRaw('employees.name, receptions.date, SUM(receptions.production_count) as total')
            ->groupBy('employees.name', 'receptions.date')
            ->orderBy('employees.name')
            ->get();

        $series = [];
        foreach ($empData as $row) {
            $dateKey = is_string($row->date) ? substr($row->date, 0, 10) : $row->date->format('Y-m-d');
            $series[$row->name][$dateKey] = $row->total;
        }

        $normalized = [];
        foreach ($series as $name => $dayData) {
            $normalized[$name] = collect($dates)->map(fn($d) => $dayData[$d] ?? 0)->toArray();
        }

        return response()->json([
            'dates' => collect($dates)->map(fn($d) => date('d M', strtotime($d)))->toArray(),
            'series' => $normalized,
        ]);
    }

    /**
     * AJAX endpoint: return 7-day trend data filtered by job_today
     */
    public function trendData7Days(Request $request)
    {
        $job = $request->get('job');
        $sevenDaysAgo = Carbon::today()->subDays(6);
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = Carbon::today()->subDays(6 - $i)->format('Y-m-d');
        }

        $query = Reception::selectRaw('DATE(date) as date, SUM(production_count) as total')
            ->where('date', '>=', $sevenDaysAgo);

        if ($job && $job !== 'all') {
            $query->where('job_today', $job);
        }

        $rawData = $query->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy(function ($item) {
                return is_string($item->date) ? substr($item->date, 0, 10) : $item->date->format('Y-m-d');
            });

        $result = collect($dates)->map(function ($d) use ($rawData) {
            return (int) ($rawData[$d]->total ?? 0);
        })->toArray();

        return response()->json([
            'dates' => collect($dates)->map(fn($d) => date('d M', strtotime($d)))->toArray(),
            'totals' => $result,
        ]);
    }

    /**
     * AJAX endpoint: return plant/group achievement data filtered by job_today
     */
    public function plantGroupData(Request $request)
    {
        $job = $request->get('job');
        $now = Carbon::now();

        $query = Reception::join('employees', 'receptions.employee_id', '=', 'employees.employee_id')
            ->selectRaw('employees.plant, employees.group, SUM(receptions.production_count) as total')
            ->whereMonth('receptions.date', $now->month)
            ->whereYear('receptions.date', $now->year);

        if ($job && $job !== 'all') {
            $query->where('receptions.job_today', $job);
        }

        $allPlantData = $query->groupBy('employees.plant', 'employees.group')->get();

        $result = [];
        foreach (['B', 'H', 'I', 'T'] as $p) {
            $result[$p] = $allPlantData->where('plant', $p)->pluck('total', 'group')->sortKeys();
        }

        return response()->json($result);
    }

    public function inputForm(Request $request, $plant = null)
    {
        $group = $request->get('group');
        $query = Employee::select('employee_id', 'name', 'plant', 'group');
        if ($plant) {
            $query->where('plant', $plant);
        }
        if ($group) {
            $query->where('group', $group);
        }
        $employees = $query->get();

        // TARIK DATA LIVE MONITORING (Data inputan hari ini di plant tsb)
        $liveQuery = Reception::join('employees', 'receptions.employee_id', '=', 'employees.employee_id')
            ->select(
                'receptions.id',
                'receptions.shift',
                'receptions.job_today',
                'receptions.production_count',
                'receptions.ritase_result',
                'receptions.notes',
                'receptions.photo',
                'receptions.created_at',
                'employees.name as operator_name',
                'employees.employee_id as operator_id',
                'employees.plant as emp_plant'
            )
            ->whereDate('receptions.date', Carbon::today())
            ->orderBy('receptions.created_at', 'desc');

        if ($plant) {
            $liveQuery->where('employees.plant', $plant);
        }

        $liveData = $liveQuery->get();

        // Get employee IDs that have already been inputted today
        $inputtedIds = $liveData->pluck('operator_id')->unique()->toArray();

        return view('input', compact('employees', 'plant', 'liveData', 'inputtedIds'));
    }

    public function storeInput(Request $request, $plant)
    {
        $jobToday = $request->input('job_today');

        $rules = [
            'employee_id'      => 'required|string|max:255',
            'job_today'        => 'required|string|max:255',
            'shift'            => 'required|integer|in:1,2,3',
            'notes'            => 'nullable|string|max:1000',
            'photo'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'ritase_result'    => $jobToday === 'Driver' ? 'required|integer|min:0' : 'nullable|integer|min:0',
            'production_count' => $jobToday === 'Driver' ? 'nullable|integer|min:0' : 'required|integer|min:0',
        ];

        $request->validate($rules);

        $data = [
            'employee_id'      => $request->employee_id,
            'shift'            => $request->shift,
            'ritase_result'    => $request->ritase_result ?? 0,
            'date'             => Carbon::today(),
            'production_count' => $request->production_count ?? 0,
            'job_today'        => $request->job_today,
            'notes'            => $request->notes
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/production'), $filename);
            $data['photo'] = 'uploads/production/' . $filename;
        }

        Reception::create($data);

        // Clear dashboard cache when new data is inputted
        Cache::forget('dashboard_7days_' . Carbon::today()->format('Y-m-d'));
        Cache::flush(); // Clear all trend caches

        return redirect()->route('input.form', ['plant' => $plant, 'group' => $request->get('group')])->with('success', 'Data berhasil disimpan!');
    }

    private function getCurrentShift($hour)
    {
        if ($hour >= 7 && $hour < 15) {
            return 1;
        } elseif ($hour >= 15 && $hour < 23) {
            return 2;
        } else {
            return 3;
        }
    }
    // 1. Fungsi buat nampilin data lama ke form edit
    public function editInput($plant, $id)
    {
        $data = Reception::findOrFail($id);
        $employees = Employee::where('plant', $plant)->select('id', 'employee_id', 'name', 'plant')->get();

        return view('input_edit', compact('data', 'plant', 'employees'));
    }

    // 2. Fungsi buat nyimpen perubahan datanya
    public function updateInput(Request $request, $plant, $id)
    {
        $reception = Reception::findOrFail($id);

        $updateData = [
            'employee_id'      => $request->employee_id,
            'shift'            => $request->shift,
            'job_today'        => $request->job_today,
            'production_count' => $request->production_count,
            'ritase_result'    => $request->ritase_result,
            'notes'            => $request->notes,
        ];

        // Handle photo upload on edit
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($reception->photo && file_exists(public_path($reception->photo))) {
                unlink(public_path($reception->photo));
            }
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/production'), $filename);
            $updateData['photo'] = 'uploads/production/' . $filename;
        }

        $reception->update($updateData);

        // Clear caches on data change
        Cache::flush();

        return redirect()->route('input.form', $plant)->with('success', 'Data berhasil direvisi!');
    }
}
