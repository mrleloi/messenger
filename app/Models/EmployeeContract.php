<?php

namespace App\Models;

use App\Enums\ContractEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class EmployeeContract extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'employee_contract';

    protected $fillable = [
        'employee_id',
        'job_id',
        'status',
        'memo',
        'workingTime_id',
        'company_id'
    ];

    protected $casts = [
        'status' => ContractEnum::class,
    ];

    protected $appends = [
        'status_name',
    ];

    public function getStatusNameAttribute()
    {
        return ContractEnum::getDescription(optional($this->status)->value);
    }

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function company()
    {
        return $this->belongsTo(CustomerCompany::class, 'company_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function workingTime()
    {
        return $this->belongsTo(WorkingTime::class, 'workingTime_id');
    }
}
