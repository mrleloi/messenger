<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class JobCategory extends Model
{
    use HasFactory, HasApiTokens;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'job_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'name',
        'status',
        'memo',
    ];

    protected $appends = [
        'status_name',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function job()
    {
        return $this->hasMany(Job::class,'category_id','id');
    }

    public function children()
    {
        return $this->hasMany(JobCategory::class, 'parent_id', 'id');
    }
    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
    }
}
