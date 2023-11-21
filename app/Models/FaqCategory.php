<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class FaqCategory extends Model
{
    use HasFactory , softDeletes, HasApiTokens;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'faq_categories';

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
        'name',
        'parent_id',
        'owner_id',
        'status',
        'memo',
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
        'status' => StatusEnum::class,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function getStatusNameAttribute() {
        return StatusEnum::getDescription(optional($this->status)->value);
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'category_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(FaqCategory::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(FaqCategory::class, 'parent_id', 'id');
    }
}
