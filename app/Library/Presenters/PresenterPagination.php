<?php namespace Jacopo\Library\Presenters;

use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;

class PresenterPagination extends Collection {

    protected $paginator;

    public function __construct($presenter, Paginator $paginator)
    {
        $this->paginator = $paginator;
        $collection = new Collection();
        foreach($this->paginator as $key => $resource)
        {
            $collection->put($key, new $presenter($resource));
        }

        $this->items = $collection->toArray();
    }

    public function getLinks()
    {
        return $this->paginator->links();
    }
}