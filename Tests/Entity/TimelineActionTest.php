<?php

namespace Highco\TimelineBundle\Tests\Entity;

use Symfony\Component\HttpFoundation\Request;

use Highco\TimelineBundle\Entity\TimelineAction;

class TimelineActionTest extends \PHPUnit_Framework_TestCase
{
    public function testExceedDoctrineORMProxy()
    {
        $ta = new TimelineAction();

        $class = 'Highco\TimelineBundle\Tests\Entity\StubEntityTimeline';

        $this->assertEquals($ta->exceedDoctrineORMProxy($class), $class, "no changements");

        $proxyClass = 'Highco\TimelineBundle\Tests\Entity\StubEntityTimelineActionProxy';
        $this->assertEquals($ta->exceedDoctrineORMProxy($proxyClass), $class, "not return proxy");
    }
}

class StubEntityTimelineActionProxy extends StubEntityTimeline implements \Doctrine\ORM\Proxy\Proxy {
    public function __load() {
    }

    public function __isInitialized() {
    }
}

class StubEntityTimeline {
}
