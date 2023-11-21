<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Favorite extends Model
{
    use HasFactory, HasApiTokens;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'favorites';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'employee_id',
        'favorite_id',
        'favorite_type',
        'status',
        'memo',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    protected $appends = [
        'model',
        'status_name',
    ];

    public function getModelAttribute() {
        switch($this->favorite_type){
            case 'App\Models\Job':
                return 0;
                break;
            case 'App\Models\Company':
                return 1;
                break;
            default:
                return 2;
                break;
        }
    }

    // public function employee()
    // {
    //     return $this->belongsTo(Employee::class, 'employee_id');
    // }

    // public function favorite() {
    //     return $this->morphTo();
    // }

    // public function job()
    // {
    //     return $this->belongsTo(Job::class, 'model_id');
    // }

    // public function company()
    // {
    //     return $this->belongsTo(Job::class, 'model_id');
    // }

    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
    }
}
