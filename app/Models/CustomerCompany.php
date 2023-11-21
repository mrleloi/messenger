<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class CustomerCompany extends Model
{
    use HasFactory, HasApiTokens;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customer_company';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'name',
        'postal_code',
        'prefecture',
        'city',
        'town',
        'block_number',
        'building_name_etc',
        'nearest_station',
        'company_type_id',
        'tel',
        'fax',
        'type',
        'ember_map',
        'map_url',
        'avatar',
        'status',
        'memo',
    ];

    protected $appends = [
        'address',
        'status_name',
    ];

    protected $casts = [
        'avatar' => 'array',
        'status' => StatusEnum::class,
    ];

    public function getAddressAttribute() {
        return $this->building_name_etc.' '.$this->block_number.','.$this->town.','.$this->city.' '.$this->perfecture;
    }

    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
    }

    public function job()
    {
        return $this->hasMany(Job::class, 'company_id', 'id');
    }

    public function employeeContract()
    {
        return $this->hasMany(EmployeeContract::class, 'company_id', 'id');
    }

    public function employees()
    {
        return $this->morphToMany(Employee::class, 'favorite',);
    }

    public function companyType()
    {
        return $this->belongsTo(CompanyType::class, 'company_type_id', 'id');
    }

    public function admins()
    {
        return $this->morphMany(Admin::class, 'companies');
    }
}
