<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OvertimeData;
use App\Models\Employee;
use Carbon\Carbon;

class ApiOvertimeController extends Controller
{
    /**
     * List data lembur.
     * GET /api/overtimes?status=pending
     */
    public function index(Request $request)
    {
        $query = OvertimeData::select('id', 'employee_name', 'overtime_date', 'start_time', 'end_time', 'reason', 'status', 'notes', 'approved_by', 'created_at')
            ->orderBy('overtime_date', 'desc')
            ->limit(100);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $overtimes = $query->get()->map(function ($ot) {
            $start = Carbon::parse($ot->start_time);
            $end = Carbon::parse($ot->end_time);
            if ($end->lt($start)) $end->addDay();
            $ot->duration_hours = min(7, $start->diffInHours($end));
            return $ot;
        });

        return response()->json([
            'overtimes' => $overtimes,
            'total' => $overtimes->count(),
        ]);
    }

    /**
     * Buat pengajuan lembur baru.
     * POST /api/overtimes
     * Body: { employee_name, overtime_date, start_time, end_time, reason }
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'overtime_date' => 'required|date|before_or_equal:today',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i',
            'reason'        => 'required|string|max:1000',
        ]);

        $overtime = OvertimeData::create([
            'employee_name' => $request->employee_name,
            'overtime_date' => $request->overtime_date,
            'start_time'    => $request->start_time,
            'end_time'      => $request->end_time,
            'reason'        => $request->reason,
            'status'        => 'pending',
        ]);

        return response()->json([
            'message' => 'Pengajuan lembur berhasil dikirim!',
            'data' => $overtime,
        ], 201);
    }

    /**
     * Approve lembur.
     * PATCH /api/overtimes/{id}/approve
     */
    public function approve(OvertimeData $overtime)
    {
        $overtime->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'notes'       => 'Disetujui oleh ' . auth()->user()->name,
        ]);

        return response()->json([
            'message' => 'Lembur disetujui!',
            'data' => $overtime,
        ]);
    }

    /**
     * Reject lembur.
     * PATCH /api/overtimes/{id}/reject
     * Body: { notes: "alasan penolakan" }
     */
    public function reject(Request $request, OvertimeData $overtime)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $overtime->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'notes'       => 'Ditolak: ' . $request->notes,
        ]);

        return response()->json([
            'message' => 'Lembur ditolak!',
            'data' => $overtime,
        ]);
    }

    /**
     * Hapus data lembur.
     * DELETE /api/overtimes/{id}
     */
    public function destroy(OvertimeData $overtime)
    {
        $overtime->delete();

        return response()->json([
            'message' => 'Data lembur berhasil dihapus!',
        ]);
    }

    /**
     * List nama karyawan untuk dropdown.
     * GET /api/employee-names
     */
    public function employeeNames()
    {
        $names = Employee::orderBy('name')->pluck('name');

        return response()->json([
            'names' => $names,
        ]);
    }
}
