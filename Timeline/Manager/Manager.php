<?php

namespace Highco\TimelineBundle\Timeline\Manager;

use Highco\TimelineBundle\Timeline\Manager\Pusher\PusherInterface;
use Highco\TimelineBundle\Timeline\Manager\Puller\PullerInterface;
use Highco\TimelineBundle\Timeline\Manager\Puller\PullerFilterableInterface;
use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Collection;

/**
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Manager
{
    /**
     * @var PusherInterface
     */
    protected $pusher;

    /**
     * @var PullerInterface
     */
    protected $puller;

    /**
     * @param TimelineAction $timelineAction
     *
     * @return boolean
     */
    public function push(TimelineAction $timelineAction)
    {
        return $this->pusher->push($timelineAction);
    }

    /**
     * @param string $subjectModel
     * @param string $subjectId
     * @param string $context default GLOBAL
     * @param array  $options
     *
     * @return array
     */
    public function getWall($subjectModel, $subjectId, $context = 'GLOBAL', $options = array())
    {
        $params = array(
            'subjectModel' => $subjectModel,
            'subjectId'    => $subjectId,
            'context'      => $context,
        );

        $results = new Collection($this->puller->pull('wall', $params, $options));

        if ($this->puller instanceof PullerFilterableInterface) {
            $results = $this->puller->filter($results);
        }

        return $results;
    }

    /**
     * @param string $subjectModel
     * @param string $subjectId
     * @param array $options
     *
     * @return array
     */
    public function getTimeline($subjectModel, $subjectId, $options = array())
    {
        $params = array(
            'subjectModel' => $subjectModel,
            'subjectId'    => $subjectId,
        );

        $results = new Collection($this->puller->pull('timeline', $params, $options));

        if ($this->puller instanceof PullerFilterableInterface) {
            $results = $this->puller->filter($results);
        }

        return $results;
    }

    /**
     * @param PusherInterface $pusher
     */
    public function setPusher(PusherInterface $pusher)
    {
        $this->pusher = $pusher;
    }

    /**
     * @param PullerInterface $puller
     */
    public function setPuller(PullerInterface $puller)
    {
        $this->puller = $puller;
    }
}
