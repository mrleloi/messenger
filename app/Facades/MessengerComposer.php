<?php

namespace App\Facades;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Facade;
use App\Actions\Messages\AddReaction;
use App\Actions\Messages\StoreAudioMessage;
use App\Actions\Messages\StoreDocumentMessage;
use App\Actions\Messages\StoreImageMessage;
use App\Actions\Messages\StoreMessage;
use App\Actions\Messages\StoreVideoMessage;
use App\Actions\Threads\MarkParticipantRead;
use App\Actions\Threads\SendKnock;
use App\Contracts\MessengerProvider;
use App\Models\Message;

/**
 * @method static \App\Support\MessengerComposer to($entity)
 * @method static \App\Support\MessengerComposer from(MessengerProvider $provider)
 * @method static \App\Support\MessengerComposer silent()
 * @method static \App\Support\MessengerComposer emitTyping()
 * @method static \App\Support\MessengerComposer emitStopTyping()
 * @method static \App\Support\MessengerComposer emitRead(?Message $message = null)
 * @method static \App\Support\MessengerComposer getInstance()
 * @method static StoreMessage message(string $message, ?string $replyingToId = null, ?array $extra = null)
 * @method static StoreImageMessage image(UploadedFile $image, ?string $replyingToId = null, ?array $extra = null)
 * @method static StoreDocumentMessage document(UploadedFile $document, ?string $replyingToId = null, ?array $extra = null)
 * @method static StoreAudioMessage audio(UploadedFile $audio, ?string $replyingToId = null, ?array $extra = null)
 * @method static StoreVideoMessage video(UploadedFile $video, ?string $replyingToId = null, ?array $extra = null)
 * @method static AddReaction reaction(Message $message, string $reaction)
 * @method static SendKnock knock()
 * @method static MarkParticipantRead read()
 *
 * @mixin \App\Support\MessengerComposer
 *
 * @see \App\Support\MessengerComposer
 */
class MessengerComposer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \App\Support\MessengerComposer::class;
    }
}
