<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'employee_id',
        'skill',
        'favorite',
        'job_objective',
        'memo',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function educations()
    {
        return $this->hasMany(EducationHistory::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }
}
