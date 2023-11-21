<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'company',
        'start_date',
        'end_date',
        'position',
        'description',
        'memo',
        'status',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
