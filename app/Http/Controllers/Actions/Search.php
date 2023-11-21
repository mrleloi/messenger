<?php

namespace App\Http\Controllers\Actions;

use App\Http\Collections\SearchCollection;
use App\Services\SearchProvidersService;

class Search
{
    /**
     * @param  SearchProvidersService  $search
     * @param  string|null  $query
     * @return SearchCollection
     */
    public function __invoke(SearchProvidersService $search, ?string $query = null): SearchCollection
    {
        return new SearchCollection(
            $search->search($query)->paginate(),
            $search->getSearchQuery(),
            $search->getSearchQueryItems(),
            true
        );
    }
}
