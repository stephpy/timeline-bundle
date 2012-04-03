<?php

namespace Highco\TimelineBundle\Tests\Timeline\Filter;

use Highco\TimelineBundle\Timeline\Filter\DuplicateKey;

use Highco\TimelineBundle\Timeline\Collection;
use Highco\TimelineBundle\Model\TimelineAction;

/**
 * DuplicateKeyTest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DuplicateKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testFilterNoDuplicateKey
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

    /**
     * testFilterOnlyOneDuplicateKey
     */
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

    /**
     * testFilterTwoDuplicateKeyDifferents
     */
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

    /**
     * testFilterTwoDuplicateKeyNoPriority
     */
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

    /**
     * testFilterTwoDuplicateKeyPriorityEquals
     */
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

    /**
     * testFilterTwoDuplicateKeyPriorityFirst
     */
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

    /**
     * testFilterTwoDuplicateKeyPrioritySecond
     */
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
     * @param int $id
     *
     * @return TimelineAction
     */
    private function createTimelineAction($id = 1)
    {
        $subject = $this->getMock('Highco\TimelineBundle\Tests\Fixtures\TimelineActionEntity');
        $subject ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        $cod = $this->getMock('Highco\TimelineBundle\Tests\Fixtures\TimelineActionEntity');
        $cod->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        $timeline = new TimelineAction();
        $timeline->create($subject, 'verb', $cod);

        return $timeline;
    }
}
