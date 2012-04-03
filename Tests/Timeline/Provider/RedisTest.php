<?php

namespace Highco\TimelineBundle\Tests\Timeline\Provider;

use Highco\TimelineBundle\Timeline\Provider\Redis;

class RedisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetWallNoSubjectModel()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);

        $redis = new Redis($client, $manager);
        $redis->getWall(array('subjectId' => 1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetWallNoSubjectId()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);

        $redis = new Redis($client, $manager);
        $redis->getWall(array('subjectModel' => 1));
    }

    public function testGetWall()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);

        $argumentsExpected = array('Timeline:GLOBAL:toto:1', 0, 9);

        $client->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('zRevRange'), $this->equalTo($argumentsExpected))
            ->will($this->returnValue(array(1337)));

        $manager->expects($this->once())
            ->method('getTimelineActionsForIds')
            ->with($this->equalTo(array(1337)));

        $redis = new Redis($client, $manager);
        $redis->getWall(array('subjectModel' => 'toto', 'subjectId' => 1));
    }

    public function testGetWallChangeParams()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);

        $argumentsExpected = array('TimelineNewKey:MyContext:toto:1', 5, 24);

        $client->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('zRevRange'), $this->equalTo($argumentsExpected))
            ->will($this->returnValue(array(1337)));

        $manager->expects($this->once())
            ->method('getTimelineActionsForIds')
            ->with($this->equalTo(array(1337)));

        $params = array(
            'subjectModel' => 'toto',
            'subjectId' => 1,
            'context' => 'MyContext',
        );

        $options = array(
            'offset' => 5,
            'limit' => 20,
            'key' => 'TimelineNewKey:%s:%s:%s',
        );

        $redis = new Redis($client, $manager);
        $redis->getWall($params, $options);
    }

    public function testPersist()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);

        $argumentsExpected = array(
            'Timeline:GLOBAL:SubjectModel:SubjectId', 13371337, 1337
        );
        $client->expects($this->at(0))
            ->method('__call')
            ->with($this->equalTo('zAdd'), $this->equalTo($argumentsExpected));

        $argumentsExpected = array(
            'PouetKey:MyContext:AnOtherOneModel:1337', 1313, 13
        );
        $client->expects($this->at(1))
            ->method('__call')
            ->with($this->equalTo('zAdd'), $this->equalTo($argumentsExpected));

        $ta      = $this->getMock('Highco\TimelineBundle\Entity\TimelineAction');
        $ta->expects($this->once())
            ->method('getSpreadTime')
            ->will($this->returnValue(13371337));

        $ta->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1337));

        $redis = new Redis($client, $manager, array('pipeline' => false));
        $redis->persist($ta, 'GLOBAL', 'SubjectModel', 'SubjectId');

        $ta      = $this->getMock('Highco\TimelineBundle\Entity\TimelineAction');
        $ta->expects($this->once())
            ->method('getSpreadTime')
            ->will($this->returnValue(1313));

        $ta->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(13));

        $redis->persist($ta, 'MyContext', 'AnOtherOneModel', '1337', array('key' => 'PouetKey:%s:%s:%s'));
        $redis->flush();
    }

    public function testCountKeys()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);

        $argumentsExpected = array('Timeline:GLOBAL:MySubject:MyId');
        $client->expects($this->at(0))
            ->method('__call')
            ->with($this->equalTo('zCard'), $this->equalTo($argumentsExpected))
            ->will($this->returnValue(1337));

        $argumentsExpected = array('MyTimeline:MyContext:MySubject:MyId');
        $client->expects($this->at(1))
            ->method('__call')
            ->with($this->equalTo('zCard'), $this->equalTo($argumentsExpected))
            ->will($this->returnValue(13));

        $redis  = new Redis($client, $manager, array('pipeline' => false));
        $result = $redis->countKeys('GLOBAL', 'MySubject', 'MyId');
        $this->assertEquals($result, 1337);
        $result = $redis->countKeys('MyContext', 'MySubject', 'MyId', array('key' => 'MyTimeline:%s:%s:%s'));
        $this->assertEquals($result, 13);
    }

    public function testRemove()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);

        $argumentsExpected = array('Timeline:GLOBAL:MySubject:MyId', 1);
        $client->expects($this->at(0))
            ->method('__call')
            ->with($this->equalTo('zRem'), $this->equalTo($argumentsExpected));

        $argumentsExpected = array('MyTimeline:MyContext:MySubject:MyId', 2);
        $client->expects($this->at(1))
            ->method('__call')
            ->with($this->equalTo('zRem'), $this->equalTo($argumentsExpected));

        $redis  = new Redis($client, $manager, array('pipeline' => false));
        $redis->remove('GLOBAL', 'MySubject', 'MyId', 1);
        $redis->remove('MyContext', 'MySubject', 'MyId', 2, array('key' => 'MyTimeline:%s:%s:%s'));
        $redis->flush();
    }

    public function testRemoveAll()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);

        $argumentsExpected = array('Timeline:GLOBAL:MySubject:MyId');
        $client->expects($this->at(0))
            ->method('__call')
            ->with($this->equalTo('del'), $this->equalTo($argumentsExpected));

        $argumentsExpected = array('MyTimeline:MyContext:MySubject:MyId');
        $client->expects($this->at(1))
            ->method('__call')
            ->with($this->equalTo('del'), $this->equalTo($argumentsExpected));

        $redis  = new Redis($client, $manager, array('pipeline' => false));
        $redis->removeAll('GLOBAL', 'MySubject', 'MyId');
        $redis->removeAll('MyContext', 'MySubject', 'MyId', array('key' => 'MyTimeline:%s:%s:%s'));
        $redis->flush();
    }

    public function testFlushNoCall()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);

        $client->expects($this->never())
            ->method('__call');

        $redis  = new Redis($client, $manager, array('pipeline' => false));
        $redis->flush();
    }

    public function testFlushNoPipeline()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);

        $argumentsExpected = array('MyTimeline:MyContext:MySubject:MyId');
        $client->expects($this->at(0))
            ->method('__call')
            ->with($this->equalTo('del'), $this->equalTo($argumentsExpected))
            ->will($this->returnValue('DELOK'));

        $argumentsExpected = array('MyTimeline:My2Context:MySubject:MyId');
        $client->expects($this->at(1))
            ->method('__call')
            ->with($this->equalTo('del'), $this->equalTo($argumentsExpected))
            ->will($this->returnValue('DELNOTOK'));

        $redis  = new Redis($client, $manager, array('pipeline' => false));
        $redis->removeAll('MyContext', 'MySubject', 'MyId', array('key' => 'MyTimeline:%s:%s:%s'));
        $redis->removeAll('My2Context', 'MySubject', 'MyId', array('key' => 'MyTimeline:%s:%s:%s'));
        $replies = $redis->flush();
        $this->assertEquals(array('DELOK', 'DELNOTOK'), $replies);
    }

    public function testFlushPipeline()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);
        $pipeline = $this->getMock('Highco\TimelineBundle\Tests\Timeline\Provider\RedisPipeline');

        $pipeline->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(array('DELOK', 'DELNOTOK')));

        $client->expects($this->once())
            ->method('pipeline')
            ->will($this->returnValue($pipeline));

        $argumentsExpected = array('MyTimeline:MyContext:MySubject:MyId');
        $pipeline->expects($this->at(0))
            ->method('__call')
            ->with($this->equalTo('del'), $this->equalTo($argumentsExpected));

        $argumentsExpected = array('MyTimeline:My2Context:MySubject:MyId');
        $pipeline->expects($this->at(1))
            ->method('__call')
            ->with($this->equalTo('del'), $this->equalTo($argumentsExpected));

        $redis  = new Redis($client, $manager);
        $redis->removeAll('MyContext', 'MySubject', 'MyId', array('key' => 'MyTimeline:%s:%s:%s'));
        $redis->removeAll('My2Context', 'MySubject', 'MyId', array('key' => 'MyTimeline:%s:%s:%s'));
        $replies = $redis->flush();

        $this->assertEquals(array('DELOK', 'DELNOTOK'), $replies);
    }

    public function testGetKey()
    {
        $client  = $this->getMock('Predis\Client');
        $manager = $this->getMock('Highco\TimelineBundle\Entity\TimelineActionManager', array(), array(), '', false);

        $redis   = new Redis($client, $manager);

        $this->assertEquals($redis->getKey('Toto', 'Subject', '1', 'Timeline:%s:%s:%s'), 'Timeline:Toto:Subject:1');
        $this->assertEquals($redis->getKey('Tata', 'MySubject', '2', 'MyTimeline:%s:%s:%s'), 'MyTimeline:Tata:MySubject:2');
    }
}

class RedisPipeline {
    public function __call($method, $arguments){}
    public function execute(){}
}
