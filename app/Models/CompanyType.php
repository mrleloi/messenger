<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class CompanyType extends Model
{
    use HasFactory, HasApiTokens;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'company_types';

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

    public function company()
    {
        return $this->hasMany(CustomerCompany::class, 'company_type_id', 'id');
    }

    public function myCompany()
    {
        return $this->hasMany(MyCompany::class, 'company_type_id', 'id');
    }

    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
    }
}
