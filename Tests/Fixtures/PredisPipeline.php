<?php

namespace Highco\TimelineBundle\Tests\Fixtures;

/**
 * PredisPipeline
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class PredisPipeline extends \Predis\Pipeline\PipelineContext
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
     * @param mixed $callable callable
     */
    public function execute($callable = null)
    {
    }

    /**
     * @param string $key key
     */
    public function del($key)
    {
    }
}
