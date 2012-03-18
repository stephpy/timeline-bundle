<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Provider\ProviderInterface;

/**
 * Puller retrieved on local by using provider
 *
 * @uses AbstractPuller
 * @uses PullerInterface
 * @uses PullerFilterableInterface
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
     * @param string $type    (wall|timeline)
     * @param array  $params  parameters to give to the provider
     * @param array  $options optional
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function pull($type, $params, $options = array())
    {
        switch($type) {
            case 'wall':
                $result = $this->provider->getWall($params, $options);
                break;
            case 'timeline':
                $result = $this->provider->getTimeline($params, $options);
                break;
            default:
                throw new \InvalidArgumentException('Unknown type on '.__CLASS__);
                break;
        }

        return $result;
    }
}
