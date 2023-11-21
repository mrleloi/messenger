<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ContactStatusEnum;

class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'name',
        'email',
        'phone_number',
        'description',
        'status',
        'memo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => ContactStatusEnum::class,
    ];

    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [
        'status_name',
    ];

    public function getStatusNameAttribute() {
        return ContactStatusEnum::getDescription(optional($this->status)->value);
    }
}
