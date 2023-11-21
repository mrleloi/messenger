<?php

namespace App\Actions\Messages;

use Illuminate\Contracts\Events\Dispatcher;
use App\Actions\BaseMessengerAction;
use App\Broadcasting\EmbedsRemovedBroadcast;
use App\Contracts\BroadcastDriver;
use App\Events\EmbedsRemovedEvent;
use App\Messenger;
use App\Models\Message;
use App\Models\Thread;

class RemoveEmbeds extends BaseMessengerAction
{
    /**
     * @var BroadcastDriver
     */
    private BroadcastDriver $broadcaster;

    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * RemoveEmbeds constructor.
     *
     * @param  BroadcastDriver  $broadcaster
     * @param  Dispatcher  $dispatcher
     * @param  Messenger  $messenger
     */
    public function __construct(BroadcastDriver $broadcaster,
                                Dispatcher $dispatcher,
                                Messenger $messenger)
    {
        $this->broadcaster = $broadcaster;
        $this->dispatcher = $dispatcher;
        $this->messenger = $messenger;
    }

    /**
     * Set embeds to false on the given message.
     *
     * @param  Thread  $thread
     * @param  Message  $message
     * @return $this
     */
    public function execute(Thread $thread, Message $message): self
    {
        $this->setThread($thread)
            ->setMessage($message)
            ->setEmbedsToFalse()
            ->fireBroadcast()
            ->fireEvents();

        return $this;
    }

    /**
     * @return $this
     */
    private function setEmbedsToFalse(): self
    {
        $this->getMessage()->update([
            'embeds' => false,
        ]);

        return $this;
    }

    /**
     * @return array
     */
    private function generateBroadcastResource(): array
    {
        return [
            'message_id' => $this->getMessage()->id,
        ];
    }

    /**
     * @return $this
     */
    private function fireBroadcast(): self
    {
        if ($this->shouldFireBroadcast()) {
            $this->broadcaster
                ->toPresence($this->getThread())
                ->with($this->generateBroadcastResource())
                ->broadcast(EmbedsRemovedBroadcast::class);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function fireEvents(): void
    {
        if ($this->shouldFireEvents()) {
            $this->dispatcher->dispatch(new EmbedsRemovedEvent(
                $this->messenger->getProvider(true),
                $this->getMessage(true)
            ));
        }
    }
}
