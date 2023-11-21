<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class AccessLog extends Model
{
    use HasFactory, HasApiTokens;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'access_logs';

     /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'time',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'type',
        'description',
        'user',
        'method',
        'url',
        'route',
        'ip_address',
        'platform',
        'browser',
        'language',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * Get the time attribute.
     *
     * @return string
     */
    public function getTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
