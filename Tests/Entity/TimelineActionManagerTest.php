<?php

namespace Spy\TimelineBundle\Tests\Entity;

/**
 * TimelineActionManagerTest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineActionManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdate()
    {
        $em = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $ta = $this->getMock('Spy\TimelineBundle\Model\TimelineAction');

        $em->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($ta));

        $em->expects($this->once())
            ->method('flush');

        $manager = new \Spy\TimelineBundle\Entity\TimelineActionManager($em, 'ModelClass');
        $manager->updateTimelineAction($ta);
    }

    public function testGetTimelineActionsForIds()
    {
        $ids = array();

        $em = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $manager = new \Spy\TimelineBundle\Entity\TimelineActionManager($em, 'ModelClass');
        $results = $manager->getTimelineActionsForIds($ids);
        $this->assertEquals($ids, array());
    }
}
