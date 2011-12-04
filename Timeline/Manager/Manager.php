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
