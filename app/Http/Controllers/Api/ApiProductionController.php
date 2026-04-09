<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reception;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ApiProductionController extends Controller
{
    /**
     * List karyawan, bisa filter by plant & group.
     * Tandai siapa yang sudah input hari ini.
     * GET /api/employees?plant=B&group=A
     */
    public function employees(Request $request)
    {
        $plant = $request->get('plant');
        $group = $request->get('group');

        $query = Employee::select('id', 'employee_id', 'name', 'plant', 'group', 'default_status', 'primary_job_type');

        if ($plant) {
            $query->where('plant', $plant);
        }
        if ($group) {
            $query->where('group', $group);
        }

        $employees = $query->orderBy('name')->get();

        // Cek siapa yang sudah input hari ini
        $inputtedToday = Reception::whereDate('date', Carbon::today())
            ->pluck('employee_id')
            ->unique()
            ->toArray();

        $employees = $employees->map(function ($emp) use ($inputtedToday) {
            $emp->already_inputted = in_array($emp->employee_id, $inputtedToday);
            return $emp;
        });

        return response()->json([
            'employees' => $employees,
            'plants' => ['B', 'H', 'I', 'T'],
            'groups' => ['A', 'B', 'C', 'D'],
        ]);
    }

    /**
     * Simpan input produksi baru.
     * POST /api/input/{plant}
     * Body: { employee_id, shift, job_today, production_count, ritase_result, notes }
     */
    public function store(Request $request, $plant)
    {
        $jobToday = $request->input('job_today');

        $rules = [
            'employee_id'      => 'required|string|max:255',
            'job_today'        => 'required|string|max:255',
            'shift'            => 'required|integer|in:1,2,3',
            'notes'            => 'nullable|string|max:1000',
            'ritase_result'    => $jobToday === 'Driver' ? 'required|integer|min:0' : 'nullable|integer|min:0',
            'production_count' => $jobToday === 'Driver' ? 'nullable|integer|min:0' : 'required|integer|min:0',
        ];

        $request->validate($rules);

        $reception = Reception::create([
            'employee_id'      => $request->employee_id,
            'shift'            => $request->shift,
            'ritase_result'    => $request->ritase_result ?? 0,
            'date'             => Carbon::today(),
            'production_count' => $request->production_count ?? 0,
            'job_today'        => $request->job_today,
            'notes'            => $request->notes,
        ]);

        // Clear dashboard cache
        Cache::flush();

        return response()->json([
            'message' => 'Data berhasil disimpan!',
            'data' => $reception,
        ], 201);
    }

    /**
     * Data live monitoring hari ini.
     * GET /api/live-data/{plant?}
     */
    public function liveData(Request $request, $plant = null)
    {
        $query = Reception::join('employees', 'receptions.employee_id', '=', 'employees.employee_id')
            ->select(
                'receptions.id',
                'receptions.shift',
                'receptions.job_today',
                'receptions.production_count',
                'receptions.ritase_result',
                'receptions.notes',
                'receptions.created_at',
                'employees.name as operator_name',
                'employees.employee_id as operator_id',
                'employees.plant'
            )
            ->whereDate('receptions.date', Carbon::today())
            ->orderBy('receptions.created_at', 'desc');

        if ($plant) {
            $query->where('employees.plant', $plant);
        }

        $data = $query->limit(200)->get();

        return response()->json([
            'data' => $data,
            'date' => Carbon::today()->format('Y-m-d'),
            'total' => $data->count(),
        ]);
    }
}
