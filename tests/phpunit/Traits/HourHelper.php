<?php  namespace Jacopo\Authentication\Tests\Unit\Traits;

use Carbon\Carbon;

trait HourHelper
{
    /**
     * @param $last_login
     * @param $expected_last
     */
    protected function assertEqualsHourFromTimestamp($last_login, $expected_last)
    {
        $this->assertEquals(Carbon::createFromFormat("Y-m-d H:i:s", $last_login)->format('H'),
                            $expected_last->format('H'), 'The two hours doesn\'t match');
    }
} 