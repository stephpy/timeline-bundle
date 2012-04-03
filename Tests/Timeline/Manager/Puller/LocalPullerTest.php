<?php

namespace Highco\TimelineBundle\Tests\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Manager\Puller\LocalPuller;

/**
 * LocalPullerTest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class LocalPullerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testPullWall
     */
    public function testPullWall()
    {
        /*$provider = $this->getMock('Highco\TimelineBundle\Timeline\Provider\Redis', array(), array(), '', false);
        $manager  = $this->getMock('Highco\TimelineBundle\Entity\timelineActionManager', array(), array(), '', false);

        $params = array(
            'paramKey' => 'paramValue',
        );
        $options = array(
            'optionKey' => 'optionValue',
        );

        $provider->expects($this->once())
            ->method('getWall')
            ->with($this->equalTo($params), $this->equalTo($options))
            ->will($this->returnValue('It Works'));

        $puller  = new LocalPuller($provider, $manager);
        $result  = $puller->pull('wall', $params, $options);

        $this->assertEquals($result, 'It Works');*/
    }

    /**
     * testPullTimeline
     */
    public function testPullTimeline()
    {
        /*$provider = $this->getMock('Highco\TimelineBundle\Timeline\Provider\Redis', array(), array(), '', false);
        $manager  = $this->getMock('Highco\TimelineBundle\Entity\timelineActionManager', array(), array(), '', false);

        $params = array(
            'paramKey' => 'paramValue',
        );
        $options = array(
            'optionKey' => 'optionValue',
        );

        $manager->expects($this->once())
            ->method('getTimeline')
            ->with($this->equalTo($params), $this->equalTo($options))
            ->will($this->returnValue('It Works'));

        $puller  = new LocalPuller($provider, $manager);
        $result  = $puller->pull('timeline', $params, $options);

        $this->assertEquals($result, 'It Works');*/
    }

    /**
     * testPullAnOtherOne
     */
    public function testPullAnOtherOne()
    {
        /*$provider = $this->getMock('Highco\TimelineBundle\Timeline\Provider\Redis', array(), array(), '', false);
        $manager  = $this->getMock('Highco\TimelineBundle\Entity\timelineActionManager', array(), array(), '', false);

        $puller  = new LocalPuller($provider, $manager);
        $result  = $puller->pull('pouet', array());*/
    }
}
