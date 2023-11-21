<?php

namespace App\Actions\Calls;

use App\Actions\BaseMessengerAction;
use App\Contracts\VideoDriver;
use App\Exceptions\CallBrokerException;
use App\Models\Call;
use App\Models\Thread;

class CallBrokerSetup extends BaseMessengerAction
{
    /**
     * @var VideoDriver
     */
    private VideoDriver $videoDriver;

    /**
     * CallBrokerSetup constructor.
     *
     * @param  VideoDriver  $videoDriver
     */
    public function __construct(VideoDriver $videoDriver)
    {
        $this->videoDriver = $videoDriver;
    }

    /**
     * Setup the call with the video provider.
     *
     * @param  Thread  $thread
     * @param  Call  $call
     * @return $this
     *
     * @throws CallBrokerException
     */
    public function execute(Thread $thread, Call $call): self
    {
        $this->setThread($thread)
            ->setCall($call)
            ->checkCallNeedsToBeSetup();

        if ($this->getCall()->isActive()) {
            $this->setupCallWithProvider()->updateCall();
        }

        return $this;
    }

    /**
     * @return void
     *
     * @throws CallBrokerException
     */
    private function checkCallNeedsToBeSetup(): void
    {
        if ($this->getCall()->isSetup()) {
            $this->throwSetupFailed('Call does not need to be setup.');
        }
    }

    /**
     * @return $this
     *
     * @throws CallBrokerException
     */
    private function setupCallWithProvider(): self
    {
        if (! $this->videoDriver->create($this->getThread(), $this->getCall())) {
            $this->throwSetupFailed('Setup with video provider failed.');
        }

        return $this;
    }

    /**
     * @param  string  $message
     *
     * @throws CallBrokerException
     */
    private function throwSetupFailed(string $message): void
    {
        throw new CallBrokerException($message);
    }

    /**
     * Update the call with the information we received from our video provider.
     *
     * @return void
     */
    private function updateCall(): void
    {
        $this->setData(
            $this->getCall()
                ->update([
                    'setup_complete' => true,
                    'room_id' => $this->videoDriver->getRoomId(),
                    'room_pin' => $this->videoDriver->getRoomPin(),
                    'room_secret' => $this->videoDriver->getRoomSecret(),
                    'payload' => $this->videoDriver->getExtraPayload(),
                ])
        );
    }
}
