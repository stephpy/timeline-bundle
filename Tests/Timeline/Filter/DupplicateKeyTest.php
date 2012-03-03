<?php

namespace Highco\TimelineBundle\Tests\Timeline\Filter;

use Highco\TimelineBundle\Timeline\Filter\DupplicateKey;

use Highco\TimelineBundle\Timeline\Collection;
use Highco\TimelineBundle\Model\TimelineAction;

class DupplicateKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testFilterNoDupplicateKey
     *
     * @access public
     * @return void
     */
    public function testFilterNoDupplicateKey()
    {
        $timelines = array(
            $this->createTimelineAction(1),
            $this->createTimelineAction(2),
        );

        $coll = new Collection($timelines);

        $filter = new DupplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 2);
        $this->assertEquals($coll->getInitialCount(), 2);
        foreach ($coll as $result) {
            $this->assertEquals($result->isDupplicated(), false);
        }
    }

    public function testFilterOnlyOneDupplicateKey()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDupplicateKey(1);

        $timelines = array(
            $t1,
            $this->createTimelineAction(2),
        );

        $coll = new Collection($timelines);

        $filter = new DupplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 2);
        $this->assertEquals($coll->getInitialCount(), 2);
        foreach ($coll as $result) {
            $this->assertEquals($result->isDupplicated(), false);
        }
    }

    public function testFilterTwoDupplicateKeyDifferents()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDupplicateKey(1);

        $t2 = $this->createTimelineAction(1);
        $t2->setDupplicateKey(2);

        $timelines = array(
            $t1,
            $t2,
        );

        $coll = new Collection($timelines);

        $filter = new DupplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 2);
        $this->assertEquals($coll->getInitialCount(), 2);
        foreach ($coll as $result) {
            $this->assertEquals($result->isDupplicated(), false);
        }
    }

    public function testFilterTwoDupplicateKeyNoPriority()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDupplicateKey(1);

        $t2 = $this->createTimelineAction(2);
        $t2->setDupplicateKey(1);

        $timelines = array(
            $t1,
            $t2,
        );

        $coll = new Collection($timelines);

        $filter = new DupplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 1);
        $this->assertEquals($coll->getInitialCount(), 2);

        $result = $coll[0];
        $this->assertEquals($result->getSubjectId(), 1);
    }

    public function testFilterTwoDupplicateKeyPriorityEquals()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDupplicateKey(1);
        $t1->setDupplicatePriority(10);

        $t2 = $this->createTimelineAction(2);
        $t2->setDupplicateKey(1);
        $t2->setDupplicatePriority(10);

        $timelines = array(
            $t1,
            $t2,
        );

        $coll = new Collection($timelines);

        $filter = new DupplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 1);
        $this->assertEquals($coll->getInitialCount(), 2);

        $result = $coll[0];
        $this->assertEquals($result->getSubjectId(), 1);
    }

    public function testFilterTwoDupplicateKeyPriorityFirst()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDupplicateKey(1);
        $t1->setDupplicatePriority(10);

        $t2 = $this->createTimelineAction(2);
        $t2->setDupplicateKey(1);
        $t2->setDupplicatePriority(5);

        $timelines = array(
            $t1,
            $t2,
        );

        $coll = new Collection($timelines);

        $filter = new DupplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 1);
        $this->assertEquals($coll->getInitialCount(), 2);

        $result = $coll[0];
        $this->assertEquals($result->getSubjectId(), 1);
    }

    public function testFilterTwoDupplicateKeyPrioritySecond()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDupplicateKey(1);
        $t1->setDupplicatePriority(5);

        $t2 = $this->createTimelineAction(2);
        $t2->setDupplicateKey(1);
        $t2->setDupplicatePriority(10);

        $timelines = array(
            $t1,
            $t2,
        );

        $coll = new Collection($timelines);

        $filter = new DupplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 1);
        $this->assertEquals($coll->getInitialCount(), 2);

        $result = $coll[1];
        $this->assertEquals($result->getSubjectId(), 2);
    }

    /**
     * createTimelineAction
     *
     * @param int $id
     * @access private
     * @return void
     */
    private function createTimelineAction($id = 1) {
        $subject = $this->getMock('Highco\TimelineBundle\Tests\Timeline\Filter\EntityStub');
        $subject ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        $cod = $this->getMock('Highco\TimelineBundle\Tests\Timeline\Filter\EntityStub');
        $cod->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        $timeline = new TimelineAction();
        $timeline->create($subject, 'verb', $cod);

        return $timeline;
    }
}

class EntityStub {
    public function getId(){
    }
}
