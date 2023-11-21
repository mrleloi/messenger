<?php

namespace App\Http\Controllers\Actions;

use App\Exceptions\ProviderNotFoundException;
use App\Http\Resources\RecipientThreadResource;
use App\Services\ThreadLocatorService;

class FindRecipientThread
{
    /**
     * Attempt to locate an existing private thread given
     * the alias and id given of another provider.
     *
     * @param  ThreadLocatorService  $locator
     * @param  string  $alias
     * @param  string  $id
     * @return RecipientThreadResource
     *
     * @throws ProviderNotFoundException
     */
    public function __invoke(ThreadLocatorService $locator,
                             string $alias,
                             string $id): RecipientThreadResource
    {
        $locator->setAlias($alias)->setId($id)->locate();

        if (! $locator->getRecipient()) {
            $locator->throwNotFoundError();
        }

        return new RecipientThreadResource(
            $locator->getRecipient(),
            $locator->getThread()
        );
    }
}
