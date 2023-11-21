<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class MyCompany extends Model
{
    use HasFactory, HasApiTokens;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'my_company';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'name',
        'postal_code',
        'prefecture',
        'company_type_id',
        'city',
        'town',
        'block_number',
        'building_name_etc',
        'nearest_station',
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
        return $this->building_name_etc.' '.$this->block_number.' '.$this->town.' '.$this->city.' '.$this->perfecture;
    }

    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
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
