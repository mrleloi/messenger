<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class WorkingTime extends Model
{
    use HasFactory, HasApiTokens;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'working_time';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'job_id',
        'check_in',
        'check_out',
        'status',
        'memo',
    ];

    protected $casts = [
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'status' => StatusEnum::class,
    ];

    protected $appends = [
        'status_name',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function breakTime() {
        return $this->hasMany(BreakTime::class);
    }

    public function employeeContract()
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
    }
}
