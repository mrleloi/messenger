<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use App\Contracts\MessengerProvider;
use App\Database\Factories\PendingFriendFactory;
use App\Facades\Messenger;
use App\Traits\ScopesProvider;
use App\Traits\Uuids;

/**
 * @mixin Model|\Eloquent
 *
 * @property string $id
 * @property string|int $sender_id
 * @property string $sender_type
 * @property string|int $recipient_id
 * @property string $recipient_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MessengerProvider $recipient
 * @property-read MessengerProvider $sender
 *
 * @method static PendingFriendFactory factory(...$parameters)
 */
class PendingFriend extends Friend
{
    use HasFactory,
        ScopesProvider;

    /**
     * @var array
     */
    protected $guarded = [];
    protected $appends = ['sender'];

    public function getSenderAttribute() {
        return (messenger()->findAliasProvider($this->user1_model))::find($this->user1_id);
    }

    public function getRecipientIdAttribute() {
        return $this->user2_id;
    }

    public function getRecipientTypeAttribute() {
        return $this->user2_model;
    }

    /**
     * Compare the recipient relation to the
     * current provider to see if they match.
     *
     * @return bool
     */
    public function isRecipientCurrentProvider(): bool
    {
        if (! Messenger::isProviderSet()) {
            return false;
        }

        return (string) Messenger::getProvider()->getKey() === (string) $this->recipient_id
            && Messenger::getProvider()->getMorphClass() === $this->recipient_type;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return PendingFriendFactory::new();
    }
}
