<?php

namespace Spy\TimelineBundle\Tests\Entity;

use Spy\TimelineBundle\Entity\TimelineAction;

/**
 * TimelineActionTest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineActionTest extends \PHPUnit_Framework_TestCase
{
    public function testExceedDoctrineORMProxy()
    {
        $ta = new TimelineAction();

        $class = 'Spy\TimelineBundle\Tests\Fixtures\TimelineActionEntity';

        $this->assertEquals($ta->exceedDoctrineORMProxy($class), $class, "no changements");

        $proxyClass = 'Spy\TimelineBundle\Tests\Fixtures\ProxyTimelineActionEntity';
        $this->assertEquals($ta->exceedDoctrineORMProxy($proxyClass), $class, "not return proxy");
    }
}
