<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use App\Models\RoomMember;
use App\Models\Message as ChatMessage;

class ChatRoom extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'chat_rooms';

    protected $fillable = [
        'name',
        'channel_name',
        'type',
    ];

    protected $appends = [
        'avatar',
        'last_message',
    ];

    public function members()
    {
        return $this->hasMany(RoomMember::class, 'room_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'room_id');
    }

    public function getAvatarAttribute()
    {
        return asset('assets/admin/image/default_user.png');
    }

    public function getLastMessageAttribute()
    {
        $lastMessage = $this->messages()->latest()->first();
        if ($lastMessage) {
            $mess  = $lastMessage->sender()->first()->user()->name . ' : ' . $lastMessage->content;
            // get first 20 characters of the message
            $data = substr($mess, 0, 30) . '...';
            return $data;
        }
        return null;
    }
}
