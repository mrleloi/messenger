<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LtuLanguage extends Model
{
    use HasFactory;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ltu_languages';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'name',
        'code',
    ];

    protected $appends = [
        'code_name',
    ];

    public function getCodeNameAttribute()
    {
        return $this->code . ' - ' . $this->name;
    }

    public function admin()
    {
        return $this->hasMany(Admin::class, 'language_id', 'id');
    }
}
