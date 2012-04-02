<?php

namespace Highco\TimelineBundle\Tests\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Manager\Pusher\LocalPusher;
use Highco\TimelineBundle\Timeline\Spread\Deployer;

class LocalPusherTest extends \PHPUnit_Framework_TestCase
{
    public function testPushLater()
    {
        $manager  = $this->getMock('Highco\TimelineBundle\Entity\timelineActionManager', array(), array(), '', false);
        $deployer = $this->getMock('Highco\TimelineBundle\Timeline\Spread\Deployer', array(), array(), '', false);
        $ta       = $this->getMock('Highco\TimelineBundle\Model\TimelineAction');

        $manager->expects($this->once())
            ->method('updateTimelineAction')
            ->with($this->equalTo($ta));

        $deployer->expects($this->once())
            ->method('getDelivery')
            ->will($this->returnValue(Deployer::DELIVERY_WAIT));

        $deployer->expects($this->never())
            ->method('deploy');

        $pusher = new LocalPusher($manager, $deployer);
        $result = $pusher->push($ta);

        $this->assertFalse($result);
    }

    public function testPushNow()
    {
        $manager  = $this->getMock('Highco\TimelineBundle\Entity\timelineActionManager', array(), array(), '', false);
        $deployer = $this->getMock('Highco\TimelineBundle\Timeline\Spread\Deployer', array(), array(), '', false);
        $ta       = $this->getMock('Highco\TimelineBundle\Model\TimelineAction');

        $manager->expects($this->once())
            ->method('updateTimelineAction')
            ->with($this->equalTo($ta));

        $deployer->expects($this->once())
            ->method('getDelivery')
            ->will($this->returnValue(Deployer::DELIVERY_IMMEDIATE));

        $deployer->expects($this->once())
            ->method('deploy')
            ->with($this->equalTo($ta));

        $pusher = new LocalPusher($manager, $deployer);
        $result = $pusher->push($ta);

        $this->assertTrue($result);
    }
}
