<?php

namespace Highco\TimelineBundle\Tests\Provider;

use Highco\TimelineBundle\Provider\Redis;

/**
 * PRedisTest
 *
 * @uses BaseRedis
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class PRedisTest extends BaseRedis
{
    /**
     * PHPRedis has to bee installed to launch theses tests
     *
     * @return void
     */
    protected function setUp()
    {
        if (!class_exists('\Predis\Client')) {
            $this->markTestSkipped(
                'The PHPRedis extension is not available.'
            );
        }
    }

    /**
     * @return Snc\RedisBundle\Client\Phpredis\Client mock of
     */
    protected function getRedisClientMock()
    {
        return $this->getMock('\Predis\Client', array(), array(), '', false);
    }

    /**
     * @return object mock
     */
    public function getRedisPipelineMock()
    {
        return $this->getMock('Highco\TimelineBundle\Tests\Fixtures\PRedisPipeline', array(), array(), '', false);
    }
}
