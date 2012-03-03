<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Provider\InterfaceProvider;

/**
 * @uses AbstractPuller
 * @uses InterfacePuller
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class LocalPuller extends AbstractPullerFilterable implements InterfacePuller, InterfacePullerFilterable
{
    /**
     * @var InterfaceProvider
     */
    private $provider;

    /**
     * @param InterfaceProvider $provider
     */
    public function __construct(InterfaceProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param string $type
     * @param array  $params
     * @param array  $options
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function pull($type, $params, $options = array())
    {
        switch($type) {
            case 'wall':
                return $this->provider->getWall($params, $options);
            case 'timeline':
                return $this->provider->getTimeline($params, $options);
        }

        throw new \InvalidArgumentException('Unknown type on '.__CLASS__);
    }
}
