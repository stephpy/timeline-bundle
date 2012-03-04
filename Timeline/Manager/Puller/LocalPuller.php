<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Provider\ProviderInterface;

/**
 * @uses AbstractPuller
 * @uses PullerInterface
 * @uses PullerFilterableInterface
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class LocalPuller extends AbstractPullerFilterable implements PullerInterface, PullerFilterableInterface
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @param ProviderInterface $provider
     */
    public function __construct(ProviderInterface $provider)
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
