<?php

namespace Highco\TimelineBundle\Timeline\Manager\Pusher;

use Highco\TimelineBundle\Model\TimelineAction;

use Doctrine\Common\Persistence\ObjectManager;
use Highco\TimelineBundle\Timeline\Spread\Deployer;

class LocalPusher implements InterfacePusher
{
	private $em;
	private $deployer;

	/**
	 * __construct
	 *
	 * @param ObjectManager $em
	 * @param Deployer $deployer
	 */
	public function __construct(ObjectManager $em, Deployer $deployer)
	{
		$this->em       = $em;
		$this->deployer = $deployer;
	}

	/**
	 * push
	 *
	 * @param TimelineAction $timeline_action
	 * @return void
	 */
	public function push(TimelineAction $timeline_action)
	{
		$this->em->persist($timeline_action);
		$this->em->flush();

		if($this->deployer->getDelivery() == Deployer::DELIVERY_IMMEDIATE)
		{
			$this->deployer->deploy($timeline_action);
		}
	}
}
