<?php

namespace Highco\TimelineBundle\Tests\Model;

use Symfony\Component\HttpFoundation\Request;

use Highco\TimelineBundle\Model\TimelineAction;

/**
 * TimelineActionTest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testConstruct
     */
    public function testConstruct()
    {
        $action = new TimelineAction();
        $this->assertEquals($action->getCreatedAt(), new \DateTime());
        $this->assertEquals($action->getStatusCurrent(), TimelineAction::STATUS_PENDING);
        $this->assertEquals($action->getStatusWanted(), TimelineAction::STATUS_PUBLISHED);
    }

    /**
     * testIsPublished
     */
    public function testIsPublished()
    {
        $action = new TimelineAction();
        $action->setStatusCurrent(TimelineAction::STATUS_PENDING);
        $this->assertFalse($action->isPublished());

        $action->setStatusCurrent(TimelineAction::STATUS_FROZEN);
        $this->assertFalse($action->isPublished());

        $action->setStatusCurrent(TimelineAction::STATUS_PUBLISHED);
        $this->assertTrue($action->isPublished());
    }

    /**
     * testHasDuplicateKey
     */
    public function testHasDuplicateKey()
    {
        $action = new TimelineAction();
        $action->setDuplicateKey(null);

        $this->assertFalse($action->hasDuplicateKey());

        $action->setDuplicateKey('toto');

        $this->assertTrue($action->hasDuplicateKey());
    }

    /**
     * testCreate
     */
    public function testCreate()
    {
        try {
            TimelineAction::create('noobject', 'verb', 'nothing');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), 'Subject should be an object');
        }

        try {
            TimelineAction::create(new TimelineAction(), 'verb', 'noobject');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), 'Direct complement should be an object');
        }

        try {
            TimelineAction::create(new TimelineAction(), 'verb', new TimelineAction(), 'no object');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), 'Indirect complement should be an object');
        }
    }

    /**
     * testIsValidStatus
     */
    public function testIsValidStatus()
    {
        $action = new TimelineAction();
        $this->assertTrue($action->isValidStatus(TimelineAction::STATUS_PENDING));
        $this->assertTrue($action->isValidStatus(TimelineAction::STATUS_PUBLISHED));
        $this->assertTrue($action->isValidStatus(TimelineAction::STATUS_FROZEN));
        $this->assertFalse($action->isValidStatus('an other one'));
    }

    /**
     * testFromRequestInvalid
     */
    public function testFromRequestInvalid()
    {
        $request = new Request();

        try {
            $subject = TimelineAction::fromRequest($request);
            $this->assertTrue(false, "This should return an exception");
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), 'You have to define subject model on "Highco\TimelineBundle\Model\TimelineAction"');
        }

        $request->query->set('subject_model', '\My\Model');

        try {
            $subject = TimelineAction::fromRequest($request);
            $this->assertTrue(false, "This should return an exception");
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals($e->getMessage(), 'You have to define subject id on "Highco\TimelineBundle\Model\TimelineAction"');
        }
    }

    /**
     * testFromRequest
     */
    public function testFromRequest()
    {
        $request = new Request();
        $request->query->set('subject_model', '\ChuckNorris');
        $request->query->set('subject_id', '1');
        $request->query->set('verb', 'own');
        $request->query->set('direct_complement_model', '\World');
        $request->query->set('direct_complement_id', '1');
        $request->query->set('indirect_complement_model', '\Vic mac key');
        $request->query->set('indirect_complement_id', '1');

        try {
            $subject = TimelineAction::fromRequest($request);

            $this->assertEquals($request->get('subject_model'), $subject->getSubjectModel());
            $this->assertEquals($request->get('subject_id'), $subject->getSubjectId());
            $this->assertEquals($request->get('verb'), $subject->getVerb());
            $this->assertEquals($request->get('direct_complement_model'), $subject->getDirectComplementModel());
            $this->assertEquals($request->get('direct_complement_id'), $subject->getDirectComplementId());
            $this->assertEquals($request->get('indirect_complement_model'), $subject->getIndirectComplementModel());
            $this->assertEquals($request->get('indirect_complement_id'), $subject->getIndirectComplementId());

        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(false, "This should not return an exception");
        }
    }

    /**
     * testSetSubject
     */
    public function testSetSubject()
    {
        $stub = $this->getMock('\Highco\TimelineBundle\Model\TimelineAction');
        $stub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('1337'));

        $action = new TimelineAction();
        $action->setSubject($stub);

        $this->assertEquals($action->getSubjectId(), 1337);
        $this->assertEquals($action->getSubjectModel(), get_class($stub));
    }

    /**
     * testSetDirectComplement
     */
    public function testSetDirectComplement()
    {
        $stub = $this->getMock('\Highco\TimelineBundle\Model\TimelineAction');
        $stub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('1337'));

        $action = new TimelineAction();
        $action->setDirectComplement($stub);

        $this->assertEquals($action->getDirectComplementId(), 1337);
        $this->assertEquals($action->getDirectComplementModel(), get_class($stub));
    }

    /**
     * testSetIndirectComplement
     */
    public function testSetIndirectComplement()
    {
        $stub = $this->getMock('\Highco\TimelineBundle\Model\TimelineAction');
        $stub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('1337'));

        $action = new TimelineAction();
        $action->setIndirectComplement($stub);

        $this->assertEquals($action->getIndirectComplementId(), 1337);
        $this->assertEquals($action->getIndirectComplementModel(), get_class($stub));
    }
}
