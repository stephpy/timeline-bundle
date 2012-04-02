<?php

namespace Highco\TimelineBundle\Tests\Timeline\Filter;

use Highco\TimelineBundle\Timeline\Filter\DataHydrator;

use Highco\TimelineBundle\Timeline\Collection;
use Highco\TimelineBundle\Model\TimelineAction;

class DataHydratorTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterNoReferences()
    {
        $timelineAction = $this->getMock('Highco\TimelineBundle\Model\TimelineAction');

        $coll         = new Collection(array($timelineAction));

        $taManager    = $this->getMock('Highco\TimelineBundle\Tests\Fixtures\TimelineActionManager');
        $dataHydrator = new DataHydrator($taManager);
        $results      = $dataHydrator->filter($coll);

        $this->assertEquals(count($results), 1, "There is one result returned");
        $this->assertEquals($results[0]->getSubject(), null, "No subject");
        $this->assertEquals($results[0]->getDirectComplement(), null, "No direct complement");
        $this->assertEquals($results[0]->getIndirectComplement(), null, "No indirect complement");
    }

    public function testFilterSubjectReference()
    {
        // object from storage
        $stdClass = new \stdClass();
        $stdClass->key = "value";

        $timelineAction = $this->getMock('Highco\TimelineBundle\Model\TimelineAction');

        $timelineAction->expects($this->any())
            ->method('getSubjectModel')
            ->will($this->returnValue('MyClass'));

        $timelineAction->expects($this->any())
            ->method('getSubjectId')
            ->will($this->returnValue('1337'));

        $timelineAction->expects($this->any())
            ->method('setSubject')
            ->with($this->equalTo($stdClass));

        $coll         = new Collection(array($timelineAction));

        $taManager    = $this->getMock('Highco\TimelineBundle\Tests\Fixtures\TimelineActionManager');

        $value = array(
            1337 => $stdClass,
        );

        $taManager->expects($this->once())
            ->method('getTimelineResultsForModelAndOids')
            ->with($this->equalTo('MyClass'), $this->equalTo(array(
                '1337' => '1337'
            )))
            ->will($this->returnValue($value));

        $dataHydrator = new DataHydrator($taManager);
        $results      = $dataHydrator->filter($coll);
    }
}
