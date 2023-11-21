<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'school',
        'start_date',
        'end_date',
        'major',
        'description',
        'memo',
        'status'
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }

}
