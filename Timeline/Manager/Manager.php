<?php

namespace Highco\TimelineBundle\Timeline\Manager;

use Highco\TimelineBundle\Timeline\Manager\Pusher\InterfacePusher;
use Highco\TimelineBundle\Timeline\Manager\Puller\InterfacePuller;
use Highco\TimelineBundle\Model\TimelineAction;

class Manager
{
	protected $pusher;
	protected $puller;

	/**
	 * push
	 *
	 * @param TimelineAction $timeline_action
	 * @access public
	 * @return void
	 */
	public function push(TimelineAction $action)
	{
		return $this->pusher->push($action);
	}

	/**
	 * getWall
	 *
	 * @param mixed $subject_model
	 * @param mixed $subject_id
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

		return $this->puller->pull('wall', $params, $options);
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

		return $this->puller->pull('timeline', $params, $options);
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
