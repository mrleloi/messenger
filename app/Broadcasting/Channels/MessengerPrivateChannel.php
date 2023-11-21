<?php

namespace App\Broadcasting\Channels;

use Illuminate\Broadcasting\Channel;
use App\Contracts\MessengerProvider;
use App\Contracts\Ownerable;
use App\Facades\Messenger;

class MessengerPrivateChannel extends Channel
{
    /**
     * Create a new private channel instance.
     *
     * @param  Ownerable|MessengerProvider  $model
     * @param  string  $channel
     */
    public function __construct($model, string $channel = '')
    {
        if ($model instanceof Ownerable) {
            $channel = $model->getOwnerPrivateChannel();
        } elseif ($model instanceof MessengerProvider) {
            $channel = Messenger::findProviderAlias($model).".{$model->getKey()}";
        }

        parent::__construct(! empty($channel) ? 'private-messenger.'.$channel : $channel);
    }
}
