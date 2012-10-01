<?php

namespace Highco\TimelineBundle\Tests\Provider;

use Highco\TimelineBundle\Provider\Redis;

/**
 * PHPRedis
 *
 * @uses BaseRedis
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class PHPRedis extends BaseRedis
{
    /**
     * PHPRedis has to bee installed to launch theses tests
     *
     * @return void
     */
    protected function setUp()
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped(
                'The PHPRedis extension is not available.'
            );
        }
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject mock of
     */
    protected function getRedisClientMock()
    {
        return $this->getMock('Snc\RedisBundle\Client\Phpredis\Client', array(), array(), '', false);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject mock
     */
    public function getRedisPipelineMock()
    {
        return $this->getMock('Highco\TimelineBundle\Tests\Fixtures\PHPRedisPipeline', array(), array(), '', false);
    }
}
