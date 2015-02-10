<?php namespace Jacopo\Library\Presenters;

use Illuminate\Support\Collection;

class PresenterCollection extends Collection {

    public function __construct($presenter, Collection $collection)
    {
        foreach($collection as $key => $resource)
        {
            $collection->put($key, new $presenter($resource));
        }

        $this->items = $collection->toArray();
    }
}