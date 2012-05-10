<?php

namespace Highco\TimelineBundle\Tests\Fixtures;

/**
 * RedisPipeline
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RedisPipeline extends \Predis\Pipeline\PipelineContext
{
    /**
     * __call
     *
     * @param string $method    Method to call
     * @param array  $arguments Arguments
     */
    public function __call($method, $arguments)
    {
    }

    /**
     * execute
     */
    public function execute($callable = null)
    {
    }
}
