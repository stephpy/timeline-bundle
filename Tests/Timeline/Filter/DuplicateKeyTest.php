<?php

namespace Highco\TimelineBundle\Tests\Timeline\Filter;

use Highco\TimelineBundle\Timeline\Filter\DuplicateKey;

use Highco\TimelineBundle\Timeline\Collection;
use Highco\TimelineBundle\Model\TimelineAction;

class DuplicateKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testFilterNoDuplicateKey
     *
     * @access public
     * @return void
     */
    public function testFilterNoDuplicateKey()
    {
        $timelines = array(
            $this->createTimelineAction(1),
            $this->createTimelineAction(2),
        );

        $coll = new Collection($timelines);

        $filter = new DuplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 2);
        $this->assertEquals($coll->getInitialCount(), 2);
        foreach ($coll as $result) {
            $this->assertEquals($result->isDuplicated(), false);
        }
    }

    public function testFilterOnlyOneDuplicateKey()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDuplicateKey(1);

        $timelines = array(
            $t1,
            $this->createTimelineAction(2),
        );

        $coll = new Collection($timelines);

        $filter = new DuplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 2);
        $this->assertEquals($coll->getInitialCount(), 2);
        foreach ($coll as $result) {
            $this->assertEquals($result->isDuplicated(), false);
        }
    }

    public function testFilterTwoDuplicateKeyDifferents()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDuplicateKey(1);

        $t2 = $this->createTimelineAction(1);
        $t2->setDuplicateKey(2);

        $timelines = array(
            $t1,
            $t2,
        );

        $coll = new Collection($timelines);

        $filter = new DuplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 2);
        $this->assertEquals($coll->getInitialCount(), 2);
        foreach ($coll as $result) {
            $this->assertEquals($result->isDuplicated(), false);
        }
    }

    public function testFilterTwoDuplicateKeyNoPriority()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDuplicateKey(1);

        $t2 = $this->createTimelineAction(2);
        $t2->setDuplicateKey(1);

        $timelines = array(
            $t1,
            $t2,
        );

        $coll = new Collection($timelines);

        $filter = new DuplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 1);
        $this->assertEquals($coll->getInitialCount(), 2);

        $result = $coll[0];
        $this->assertEquals($result->getSubjectId(), 1);
    }

    public function testFilterTwoDuplicateKeyPriorityEquals()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDuplicateKey(1);
        $t1->setDuplicatePriority(10);

        $t2 = $this->createTimelineAction(2);
        $t2->setDuplicateKey(1);
        $t2->setDuplicatePriority(10);

        $timelines = array(
            $t1,
            $t2,
        );

        $coll = new Collection($timelines);

        $filter = new DuplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 1);
        $this->assertEquals($coll->getInitialCount(), 2);

        $result = $coll[0];
        $this->assertEquals($result->getSubjectId(), 1);
    }

    public function testFilterTwoDuplicateKeyPriorityFirst()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDuplicateKey(1);
        $t1->setDuplicatePriority(10);

        $t2 = $this->createTimelineAction(2);
        $t2->setDuplicateKey(1);
        $t2->setDuplicatePriority(5);

        $timelines = array(
            $t1,
            $t2,
        );

        $coll = new Collection($timelines);

        $filter = new DuplicateKey();
        $coll = $filter->filter($coll);

        $this->assertEquals($coll->count(), 1);
        $this->assertEquals($coll->getInitialCount(), 2);

        $result = $coll[0];
        $this->assertEquals($result->getSubjectId(), 1);
    }

    public function testFilterTwoDuplicateKeyPrioritySecond()
    {
        $t1 = $this->createTimelineAction(1);
        $t1->setDuplicateKey(1);
        $t1->setDuplicatePriority(5);

        $t2 = $this->createTimelineAction(2);
        $t2->setDuplicateKey(1);
        $t2->setDuplicatePriority(10);

        $timelines = array(
            $t1,
            $t2,
        );

        $coll = new Collection($timelines);

        $filter = new DuplicateKey();
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
