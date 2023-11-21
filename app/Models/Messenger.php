<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use App\Contracts\MessengerProvider;
use App\Contracts\Ownerable;
use App\Database\Factories\MessengerFactory;
use App\Traits\HasOwner;
use App\Traits\ScopesProvider;
use App\Traits\Uuids;

/**
 * @mixin Model|\Eloquent
 *
 * @property string $id
 * @property bool $message_popups
 * @property bool $message_sound
 * @property bool $call_ringtone_sound
 * @property bool $notify_sound
 * @property bool $dark_mode
 * @property int $online_status
 * @property string|null $ip
 * @property string|null $timezone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static MessengerFactory factory(...$parameters)
 */
class Messenger extends Model implements Ownerable
{
    use HasFactory,
        HasOwner,
        ScopesProvider,
        Uuids;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messengers';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'message_popups' => true,
        'message_sound' => true,
        'call_ringtone_sound' => true,
        'notify_sound' => true,
        'online_status' => 1,
        'dark_mode' => true,
        'ip' => null,
        'timezone' => null,
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $hidden = [
        'ip',
        'timezone',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'message_popups' => 'boolean',
        'message_sound' => 'boolean',
        'call_ringtone_sound' => 'boolean',
        'notify_sound' => 'boolean',
        'dark_mode' => 'boolean',
        'online_status' => 'integer',
    ];

    /**
     * @return MorphTo|MessengerProvider
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return MessengerFactory::new();
    }
}
