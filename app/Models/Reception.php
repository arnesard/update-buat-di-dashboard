<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reception extends Model
{
    protected $fillable = [
        'employee_id',
        'shift',
        'ritase_result',
        'date',
        'production_count',
        'job_today',
        'notes',
        'photo'
    ];

    protected $casts = [
        'date' => 'date',
        'ritase_result' => 'integer',
        'production_count' => 'integer'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
