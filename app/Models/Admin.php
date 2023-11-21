<?php

namespace App\Models;

use App\Enums\SexEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Contracts\MessengerProvider;
use App\Traits\Messageable;
use App\Traits\Search;
use App\Traits\Uuids;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\StatusEnum;
// 23/11/13 loild_laravel_base_112_fix_login_admin add start
use Illuminate\Notifications\Notifiable;
// 23/11/13 loild_laravel_base_112_fix_login_admin add end

class Admin extends Authenticatable implements MessengerProvider
{
    // 23/11/13 loild_laravel_base_112_fix_login_admin remove start
    //use HasFactory, HasRoles, softDeletes, HasApiTokens;
    // 23/11/13 loild_laravel_base_112_fix_login_admin remove end
    // 23/11/13 loild_laravel_base_112_fix_login_admin add start
    use HasFactory, softDeletes, Notifiable,
//        HasRoles,
        HasApiTokens,
        Messageable,
        Search,
        Uuids;
    // 23/11/13 loild_laravel_base_112_fix_login_admin add end

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin';
    public string $model = 'App\Models\Admin';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable=[
        'name',
        'email',
        'password',
        'status',
        'avatar',
        'memo',
        'sex',
        'language_id',
        'country_id',
        'birthdate',
        'postal_code',
        'prefecture',
        'city',
        'town',
        'block_number',
        'building_name_etc',
        'company_type',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
   protected $appends = [
       'status_name',
       'sex_text',
       'age',
       'address',
       'model_type_name',
   ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => StatusEnum::class,
        'sex' => SexEnum::class,
        'avatar' => 'array',
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

    public function rooms()
    {
        $roomMember =  RoomMember::where('user_id', $this->id)->where('user_model', $this->model)->get();
        $rooms = [];
        foreach ($roomMember as $item) {
            $rooms[] = $item->room;
        }
        return $rooms;
    }

    // Get freinds request sent by the user
    public function friendRequestsSent()
    {
        return Friend::where('user1_id', $this->id)->where('user1_model', $this->model)->where('status', 0)->get();
    }

    // Get freinds request sent to the user
    public function friendRequestsReceived()
    {
        return Friend::where('user2_id', $this->id)->where('user2_model', $this->model)->where('status', 0)->get();
    }

    // Get all the friends of the user
    public function friends()
    {
        $friends1 = Friend::where('user1_id', $this->id)->where('user1_model', $this->model)->where('status', 1)->get();
        $friends2 = Friend::where('user2_id', $this->id)->where('user2_model', $this->model)->where('status', 1)->get();
        $friends = [];
        foreach ($friends1 as $friend) {
            $friends[] = $friend->user2();
        }
        foreach ($friends2 as $friend) {
            $friends[] = $friend->user1();
        }
        usort($friends, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });
        return $friends;
    }

    // Send a friend request
    public function sendFriendRequest($targetUser)
    {
        $friend = Friend::where('user1_id', $this->id)->where('user1_model', $this->model)->where('user2_id', $targetUser->id)->where('user2_model', $targetUser->model)->first();
        if ($friend) {
            return false;
        }
        $friend = Friend::where('user2_id', $this->id)->where('user2_model', $this->model)->where('user1_id', $targetUser->id)->where('user1_model', $targetUser->model)->first();
        if ($friend) {
            return false;
        }
        $friend = new Friend();
        $friend->user1_id = $this->id;
        $friend->user1_model = $this->model;
        $friend->user2_id = $targetUser->id;
        $friend->user2_model = $targetUser->model;
        $friend->status = 0;
        $friend->save();
        return true;
    }

    public function getSexTextAttribute()
    {
        return SexEnum::getDescription(optional($this->sex)->value);
    }

    public function getAgeAttribute()
    {
        if ($this->birthdate) {
            return date_diff(date_create($this->birthdate), date_create('today'))->y;
        }
        return null;
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function language()
    {
        return $this->belongsTo(LtuLanguage::class, 'language_id', 'id');
    }

    public function notifications()
    {
        return $this->morphToMany(Notification::class, 'notiSends');
    }

    public function getAddressAttribute() {
        return $this->building_name_etc.''.$this->block_number.','.$this->town.','.$this->city.''.$this->perfecture;
    }

    public function getModelTypeNameAttribute() {
        if($this->model_type == 1) {
            return 'App\Models\CustomerCompany';
        } else if($this->model_type == 2) {
            return 'App\Models\MyCompany';
        }
    }

    public function company()
    {
        return $this->morphTo();
    }
    /**
     * @return array
     */
    public static function getProviderSettings(): array
    {
        return [
            'alias' => 'admin',
            'searchable' => true,
            'friendable' => true,
            'devices' => false,
            'default_avatar' => public_path('vendor/messenger/images/users.png'),
            'cant_message_first' => [],
            'cant_search' => [],
            'cant_friend' => [],
        ];
    }

    /**
     * @return string
     */
    public function getProviderAvatarColumn(): string
    {
        return 'avatar';
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDemo(Builder $query): Builder
    {
        return $query->where('status', '=', 1);
    }
}
