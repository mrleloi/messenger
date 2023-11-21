<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;

class Job extends Model
{
    use HasFactory, HasApiTokens;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'job';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'image_url',
        'company_id',
        'prefecture_id',
        'title',
        'description',
        'category_id',
        'posting_date',
        'number_of_people',
        'posting_end_date',
        'basic_hourly_wage',
        'working_conditions',
        'notice',
        'belongings',
        'status',
        'memo',
        // 'owner'
    ];

    protected $appends = [
        'posting_time_text',
        'status_name'
    ];

    protected $casts = [
        'image_url' => 'array',
        'posting_date' => 'datetime:Y/m/d H:i:s',
        'posting_end_date' => 'datetime:Y/m/d H:i:s',
        'status' => StatusEnum::class,
    ];

    public function getPostingTimeTextAttribute(){
        $date = Carbon::parse($this->posting_date);
        return 'ngày '.$date->day.' tháng '.$date->month;
    }

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id', 'id');
    }

    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class, 'prefecture_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(CustomerCompany::class, 'company_id', 'id');
    }

    // public function owner()
    // {
    //     return $this->belongsTo(Admin::class, 'owner_id');
    // }

    public function jobRecruitment()
    {
        return $this->hasMany(jobRecruitment::class, 'job_id', 'id');
    }

    public function doc()
    {
        return $this->hasMany(Doc::class, 'job_id', 'id');
    }

    public function allowance()
    {
        return $this->hasMany(Allowance::class, 'job_id', 'id');
    }

    public function employees()
    {
        return $this->morphToMany(Employee::class, 'favorite');
    }

    public function comment()
    {
        return $this->hasMany(Comment::class, 'job_id', 'id');
    }

    public function workingTime()
    {
        return $this->hasMany(WorkingTime::class, 'job_id', 'id');
    }

    public function employeeContract()
    {
        return $this->hasMany(EmployeeContract::class, 'job_id', 'id');
    }

    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
    }

}
