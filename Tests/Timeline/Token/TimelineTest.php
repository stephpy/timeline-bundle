<?php

namespace Highco\TimelineBundle\Tests\Timeline\Token;

use Symfony\Component\HttpFoundation\Request;
use Highco\TimelineBundle\Timeline\Token\Timeline;

class TimelineTest extends \PHPUnit_Framework_TestCase
{
    public function testFromRequestInvalid()
    {
        $request = new Request();

        try {
            $subject = Timeline::fromRequest($request);
            $this->assertTrue(false, "This should return an exception");
        } catch(\InvalidArgumentException $e){
            $this->assertEquals($e->getMessage(), 'You have to define subject model on "Highco\TimelineBundle\Timeline\Token\Timeline"');
        }

        $request->query->set('subject_model', '\My\Model');

        try {
            $subject = Timeline::fromRequest($request);
            $this->assertTrue(false, "This should return an exception");
        } catch(\InvalidArgumentException $e){
            $this->assertEquals($e->getMessage(), 'You have to define subject id on "Highco\TimelineBundle\Timeline\Token\Timeline"');
        }
    }

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
            $subject = Timeline::fromRequest($request);

            $this->assertEquals($request->get('subject_model'), $subject->subject_model);
            $this->assertEquals($request->get('subject_id'), $subject->subject_id);
            $this->assertEquals($request->get('verb'), $subject->verb);
            $this->assertEquals($request->get('direct_complement_model'), $subject->direct_complement_model);
            $this->assertEquals($request->get('direct_complement_id'), $subject->direct_complement_id);
            $this->assertEquals($request->get('indirect_complement_model'), $subject->indirect_complement_model);
            $this->assertEquals($request->get('indirect_complement_id'), $subject->indirect_complement_id);

        } catch(\InvalidArgumentException $e){
            $this->assertTrue(false, "This should not return an exception");
        }
    }
}

