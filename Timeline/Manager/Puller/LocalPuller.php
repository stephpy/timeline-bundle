<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Provider\InterfaceProvider;

/**
 * LocalPuller
 *
 * @uses AbstractPuller
 * @uses InterfacePuller
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class LocalPuller extends AbstractPullerFilterable implements InterfacePuller, InterfacePullerFilterable
{
    private $provider;

    /**
     * __construct
     *
     * @param InterfaceProvider $provider
     */
    public function __construct(InterfaceProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * pull
     *
     * @param string $type
     * @param array $params
     * @param array $options
     * @return array or Exception
     */
    public function pull($type, $params, $options = array())
    {
        switch($type)
        {
            case 'wall':
                return $this->provider->getWall($params, $options);
                break;
            case 'timeline':
                return $this->provider->getTimeline($params, $options);
                break;
            default:
                throw new \InvalidArgumentException('Unknown type on '.__CLASS__);
                break;
        }
    }
}
