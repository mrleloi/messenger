<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Doc extends Model
{
    use HasFactory, HasApiTokens;

/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'doc';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'job_id',
        'name',
        'doc_url',
        'memo',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $casts = [
        'status' => StatusEnum::class,
    ];


    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
    }
}
