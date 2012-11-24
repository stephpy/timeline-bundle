<?php

namespace Spy\TimelineBundle\Tests\Fixtures;

/**
 * ProxyTimelineActionEntity
 *
 * @uses TimelineActionEntity
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ProxyTimelineActionEntity extends TimelineActionEntity implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * __load
     */
    public function __load()
    {
    }

    /**
     * __isInitialized
     */
    public function __isInitialized()
    {
    }

}
