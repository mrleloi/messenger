<?php

namespace App\Models;

use App\Enums\ResultEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class JobRecruitment extends Model
{
    use HasFactory, HasApiTokens;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'job_recruitment';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'job_id',
        'employee_id',
        'date',
        'date_result',
        'result',
        'status',
        'memo',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
        'result' => ResultEnum::class,
        'created_at' => 'datetime:Y/m/d H:i:s',
    ];

    /**
     * @var string[] The attributes that should be hidden for serialization.
     */
    protected $appends = [
        'status_name',
        'result_name',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
    }

    public function getResultNameAttribute() {
        return ResultEnum::getDescription(optional($this->status)->value);
    }
}
