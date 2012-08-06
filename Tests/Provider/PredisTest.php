<?php

namespace Highco\TimelineBundle\Tests\Provider;

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
                'PRedis must be installed to launch this test'
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
        return $this->getMock('\Predis\Pipeline\PipelineContext', array(), array(), '', false);
    }
}
