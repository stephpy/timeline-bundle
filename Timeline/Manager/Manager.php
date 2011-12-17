<?php

namespace Highco\TimelineBundle\Timeline\Manager;

use Highco\TimelineBundle\Timeline\Manager\Pusher\InterfacePusher;
use Highco\TimelineBundle\Timeline\Manager\Puller\InterfacePuller;
use Highco\TimelineBundle\Timeline\Manager\Puller\InterfacePullerFilterable;
use Highco\TimelineBundle\Model\TimelineAction;

/**
 * Manager
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Manager
{
	protected $pusher;
	protected $puller;

	/**
	 * push
	 *
	 * @param TimelineAction $timeline_action
	 * @return boolean
	 */
	public function push(TimelineAction $action)
	{
		return $this->pusher->push($action);
	}

	/**
	 * getWall
	 *
	 * @param string $subject_model
	 * @param string $subject_id
	 * @param string $context default GLOBAL
	 * @param array $options
	 * @return array
	 */
	public function getWall($subject_model, $subject_id, $context = "GLOBAL", $options = array())
	{
		$params = array(
			'subject_model' => $subject_model,
			'subject_id'    => $subject_id,
			'context'       => $context,
		);

		$results = $this->puller->pull('wall', $params, $options);

        if($this->puller instanceof InterfacePullerFilterable)
        {
            $results = $this->puller->filter($results);
        }

        return $results;

	}

	/**
	 * getTimeline
	 *
	 * @param string $subject_model
	 * @param string $subject_id
	 * @param array $options
	 * @return array
	 */
	public function getTimeline($subject_model, $subject_id, $options = array())
	{
		$params = array(
			'subject_model' => $subject_model,
			'subject_id'    => $subject_id,
		);

		$results = $this->puller->pull('timeline', $params, $options);

        if($this->puller instanceof InterfacePullerFilterable)
        {
            $results = $this->puller->filter($results);
        }

        return $results;
	}

	/**
	 * setPusher
	 *
	 * @param InterfacePusher $pusher
	 */
	public function setPusher(InterfacePusher $pusher)
	{
		$this->pusher = $pusher;
	}

	/**
	 * setPuller
	 *
	 * @param InterfacePuller $puller
	 */
	public function setPuller(InterfacePuller $puller)
	{
		$this->puller = $puller;
	}
}
