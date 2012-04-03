<?php

namespace Highco\TimelineBundle\Tests\Timeline\Manager;

use Highco\TimelineBundle\Timeline\Collection;
use Highco\TimelineBundle\Model\TimelineAction;

/**
 * ManagerTest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testPush
     */
    public function testPush()
    {
        $timelineAction = $this->getMock('Highco\TimelineBundle\Model\TimelineAction');

        $pusher = $this->getMock('\Highco\TimelineBundle\Timeline\Manager\Pusher\LocalPusher', array(), array(), '', false);

        $pusher->expects($this->once())
            ->method('push')
            ->will($this->returnValue(1337));

        $manager = new \Highco\TimelineBundle\Timeline\Manager\Manager();
        $manager->setPusher($pusher);

        $result = $manager->push($timelineAction);
        $this->assertEquals($result, 1337);
    }

    /**
     * testGetWall
     */
    public function testGetWall()
    {
        $puller = $this->getMock('\Highco\TimelineBundle\Timeline\Manager\Puller\LocalPuller', array(), array(), '', false);

        $coll = array(
            $this->createTimelineAction(1),
        );

        $puller->expects($this->once())
            ->method('pull')
            ->will($this->returnValue($coll));

        $puller->expects($this->once())
            ->method('filter')
            ->will($this->returnArgument(0));

        $manager = new \Highco\TimelineBundle\Timeline\Manager\Manager();
        $manager->setPuller($puller);

        $result = $manager->getWall('toto', 1);

        $this->assertEquals($result, new Collection($coll));
    }

    /**
     * testGetTimeline
     */
    public function testGetTimeline()
    {
        $puller = $this->getMock('\Highco\TimelineBundle\Timeline\Manager\Puller\LocalPuller', array(), array(), '', false);

        $coll = array(
            $this->createTimelineAction(1),
        );

        $puller->expects($this->once())
            ->method('pull')
            ->will($this->returnValue($coll));

        $puller->expects($this->once())
            ->method('filter')
            ->will($this->returnArgument(0));

        $manager = new \Highco\TimelineBundle\Timeline\Manager\Manager();
        $manager->setPuller($puller);

        $result = $manager->getTimeline('toto', 1);

        $this->assertEquals($result, new Collection($coll));
    }

    /**
     * @param int $id
     *
     * @return TimelineAction
     */
    private function createTimelineAction($id = 1)
    {
        $subject = $this->getMock('Highco\TimelineBundle\Tests\Timeline\Manager\EntityStub');
        $subject ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        $cod = $this->getMock('Highco\TimelineBundle\Tests\Timeline\Manager\EntityStub');
        $cod->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        $timeline = new TimelineAction();
        $timeline->create($subject, 'verb', $cod);

        return $timeline;
    }
}

/**
 * EntityStub
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class EntityStub
{
    /**
     * getId
     */
    public function getId()
    {
    }
}
