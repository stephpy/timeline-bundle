<?php

namespace Highco\TimelineBundle\Tests\Filter;

use Highco\TimelineBundle\Filter\DataHydrator;

use Highco\TimelineBundle\Model\Collection;
use Highco\TimelineBundle\Model\TimelineAction;

/**
 * DataHydratorTest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DataHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testFilterNoReferences
     */
    public function testFilterNoReferences()
    {
        $timelineAction = $this->getMock('Highco\TimelineBundle\Model\TimelineAction');

        $coll         = new Collection(array($timelineAction));

        $taManager    = $this->getMock('Highco\TimelineBundle\Tests\Fixtures\TimelineActionManager');
        $dataHydrator = new DataHydrator($taManager, 'orm');
        $results      = $dataHydrator->filter($coll);

        $this->assertEquals(count($results), 1, "There is one result returned");
        $this->assertEquals($results[0]->getSubject(), null, "No subject");
        $this->assertEquals($results[0]->getDirectComplement(), null, "No direct complement");
        $this->assertEquals($results[0]->getIndirectComplement(), null, "No indirect complement");
    }

    /**
     * testFilterSubjectReference
     */
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

        $em = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $taManager->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($em));

        $repository = $this->getMock('Highco\TimelineBundle\Tests\Fixtures\EntityRepository', array(), array(), '', false);

        $em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repository));

        $repository->expects($this->once())
            ->method('getTimelineResultsForModelAndOids')
            ->with($this->equalTo(array('1337' => '1337')))
            ->will($this->returnValue($value));

        $dataHydrator = new DataHydrator($taManager, 'orm');
        $results      = $dataHydrator->filter($coll);
    }
}
