<?php

namespace Highco\TimelineBundle\Tests\Entity;

use Symfony\Component\HttpFoundation\Request;

use Highco\TimelineBundle\Entity\TimelineAction;

/**
 * TimelineActionTest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testExceedDoctrineORMProxy
     */
    public function testExceedDoctrineORMProxy()
    {
        $ta = new TimelineAction();

        $class = 'Highco\TimelineBundle\Tests\Entity\StubEntityTimeline';

        $this->assertEquals($ta->exceedDoctrineORMProxy($class), $class, "no changements");

        $proxyClass = 'Highco\TimelineBundle\Tests\Entity\StubEntityTimelineActionProxy';
        $this->assertEquals($ta->exceedDoctrineORMProxy($proxyClass), $class, "not return proxy");
    }
}

/**
 * StubEntityTimelineActionProxy
 *
 * @uses StubEntityTimeline
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class StubEntityTimelineActionProxy extends StubEntityTimeline implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * __load
     */
    public function __load()
    {
    }

    /**
     * __isInitialized
     */
    public function __isInitialized()
    {
    }
}

/**
 * StubEntityTimeline
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class StubEntityTimeline
{
}
