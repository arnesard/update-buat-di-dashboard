<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OvertimeData;
use App\Models\Employee;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    public function index()
    {
        $overtimes = OvertimeData::with('approver:id,name')
            ->select('id', 'employee_name', 'overtime_date', 'start_time', 'end_time', 'reason', 'status', 'notes', 'approved_by', 'created_at', 'updated_at')
            ->orderBy('overtime_date', 'desc')
            ->limit(500)
            ->get();

        $employees = Employee::orderBy('name')->get();

        return view('overtime.index', compact('overtimes', 'employees'));
    }
    
    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        return view('overtime.create', compact('employees'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'overtime_date' => 'required|date|before_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:1000',
        ]);
        
        OvertimeData::create([
            'employee_name' => $request->employee_name,
            'overtime_date' => $request->overtime_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
        
        return redirect()->route('overtime.index')
            ->with('success', 'Pengajuan lembur berhasil dikirim!');
    }
    
    public function approve(OvertimeData $overtime)
    {
        if (auth()->user()->isLeader()) {
            return redirect()->route('overtime.index')->with('error', 'Anda tidak memiliki akses untuk menyetujui lembur.');
        }

        $overtime->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'notes'       => 'Disetujui oleh ' . auth()->user()->name,
        ]);

        return redirect()->route('overtime.index')->with('success', 'Pengajuan lembur disetujui!');
    }

    public function reject(Request $request, OvertimeData $overtime)
    {
        if (auth()->user()->isLeader()) {
            return redirect()->route('overtime.index')->with('error', 'Anda tidak memiliki akses untuk menolak lembur.');
        }

        $request->validate(['notes' => 'required|string|max:1000']);

        $overtime->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'notes'       => 'Ditolak: ' . $request->notes,
        ]);

        return redirect()->route('overtime.index')->with('success', 'Pengajuan lembur ditolak!');
    }

    public function destroy(OvertimeData $overtime)
    {
        if (auth()->user()->isLeader()) {
            return redirect()->route('overtime.index')->with('error', 'Anda tidak memiliki akses untuk menghapus data lembur.');
        }

        $overtime->delete();

        return redirect()->route('overtime.index')->with('success', 'Pengajuan lembur dihapus!');
    }
}
