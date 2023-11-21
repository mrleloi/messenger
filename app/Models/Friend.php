<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Sanctum\HasApiTokens;
use App\Facades\Messenger;

class Friend extends Model
{
    use HasFactory;
//    , HasApiTokens;

    protected $table = 'friends';

    protected $fillable = [
        'id',
        'user1_id',
        'user2_id',
        'user1_model',
        'user2_model',
        'status',
    ];

    protected $appends = ['party', 'party_id'];

    public static int $STATUS_ACCEPTED = 1;
    public static int $STATUS_PENDING = 0;

    public function getPartyAttribute() {
        $authUserId = messenger()->getProvider()->getKey();
        if ($this->user1_id == $authUserId)
            return (messenger()->findAliasProvider($this->user2_model))::find($this->user2_id);
        return (messenger()->findAliasProvider($this->user1_model))::find($this->user1_id);
    }

    public function getPartyIdAttribute() {
        $authUserId = messenger()->getProvider()->getKey();
        if ($this->user1_id == $authUserId)
            return $this->user2_id;
        return $this->user1_id;
    }

    public function user1()
    {
        if ($user = (messenger()->findAliasProvider($this->user1_model))::find($this->user1_id)) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar ? asset('storage/images/' . $user->avatar[0]) : asset('assets/admin/image/default_user.png'),
                'type' => $this->user1_model === 'App\Models\Admin' ? 'admin' : 'employee',
                'friend_id' => $this->id,
            ];
        }
        return null;
    }

    public function user2()
    {
        if ($user = (messenger()->findAliasProvider($this->user2_model))::find($this->user2_id)) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar ? asset('storage/images/'.$user->avatar[0]) : asset('assets/admin/image/default_user.png'),
                'type' => $this->user2_model === 'App\Models\Admin' ? 'admin' : 'employee',
                'friend_id' => $this->id,
            ];
        }
    }
}
