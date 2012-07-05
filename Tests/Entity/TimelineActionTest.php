<?php

namespace Highco\TimelineBundle\Tests\Entity;


use Highco\TimelineBundle\Entity\TimelineAction;

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

        $class = 'Highco\TimelineBundle\Tests\Fixtures\TimelineActionEntity';

        $this->assertEquals($ta->exceedDoctrineORMProxy($class), $class, "no changements");

        $proxyClass = 'Highco\TimelineBundle\Tests\Fixtures\ProxyTimelineActionEntity';
        $this->assertEquals($ta->exceedDoctrineORMProxy($proxyClass), $class, "not return proxy");
    }
}
