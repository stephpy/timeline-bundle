<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Provider\ProviderInterface;
use Highco\TimelineBundle\Model\TimelineActionManagerInterface;

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
     * @var TimelineActionManagerInterface
     */
    private $timelineActionManager;

    /**
     * @param ProviderInterface              $provider              Provider to pull
     * @param TimelineActionManagerInterface $timelineActionManager Manager to retrieve from local storage
     */
    public function __construct(ProviderInterface $provider, TimelineActionManagerInterface $timelineActionManager)
    {
        $this->provider              = $provider;
        $this->timelineActionManager = $timelineActionManager;
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
                $result = $this->timelineActionManager->getTimeline($params, $options);
                break;
            default:
                throw new \InvalidArgumentException('Unknown type on '.__CLASS__);
                break;
        }

        return $result;
    }
}
