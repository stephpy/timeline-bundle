<?php

namespace Highco\TimelineBundle\Timeline\Manager;

use Highco\TimelineBundle\Timeline\Manager\Pusher\InterfacePusher;
use Highco\TimelineBundle\Timeline\Manager\Puller\InterfacePuller;
use Highco\TimelineBundle\Timeline\Manager\Puller\InterfacePullerFilterable;
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
     * @var InterfacePusher
     */
    protected $pusher;

    /**
     * @var InterfacePuller
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
            'subject_model' => $subjectModel,
            'subject_id'    => $subjectId,
            'context'       => $context,
        );

        $results = new Collection($this->puller->pull('wall', $params, $options));

        if ($this->puller instanceof InterfacePullerFilterable) {
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
            'subject_model' => $subjectModel,
            'subject_id'    => $subjectId,
        );

        $results = new Collection($this->puller->pull('timeline', $params, $options));

        if ($this->puller instanceof InterfacePullerFilterable) {
            $results = $this->puller->filter($results);
        }

        return $results;
    }

    /**
     * @param InterfacePusher $pusher
     */
    public function setPusher(InterfacePusher $pusher)
    {
        $this->pusher = $pusher;
    }

    /**
     * @param InterfacePuller $puller
     */
    public function setPuller(InterfacePuller $puller)
    {
        $this->puller = $puller;
    }
}
