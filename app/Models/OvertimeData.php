<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeData extends Model
{
    protected $fillable = [
        'employee_name',
        'overtime_date',
        'start_time',
        'end_time',
        'reason',
        'status',
        'notes',
        'approved_by'
    ];
    
    protected $casts = [
        'overtime_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
