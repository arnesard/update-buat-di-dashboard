<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'employee_id',
        'plant',
        'group',
        'default_status',
        'primary_job_type',
        'department',
        'position',
        'hire_date',
        'phone',
        'address'
    ];

    protected $casts = [
        'hire_date' => 'date'
    ];
}
