<?php  namespace Jacopo\Authentication\Tests\Unit\Stubs;

use Illuminate\Log\Writer;

class NullLogger extends Writer
{
    function __construct()
    {
        // do nothing...
    }

    public function __call($method, $parameters)
    {
        // do nothing...
    }
} 