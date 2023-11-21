<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Allowance extends Model
{
    use HasFactory, HasApiTokens;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'allowance';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'name',
        'price',
        'avatar',
        'job_id',
        'status',
        'memo',
    ];

     protected $casts = [
        'avatar' => 'array',
        'status' => StatusEnum::class,
     ];

    /**
     * @var string[] The attributes that should be hidden for serialization.
     */
    protected $appends = [
        'status_name',
    ];


    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
    }
}
