<?php

namespace Highco\TimelineBundle\Timeline\Spread;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Provider\InterfaceProvider;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Deployer
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Deployer
{
	CONST DELIVERY_IMMEDIATE = "immediate";
	CONST DELIVERY_WAIT      = "wait";

	private $delivery = "immediate";
	private $spread_manager;
	private $provider;
	private $em;

	/**
	 * __construct
	 *
	 * @param Manager $spread_manager
	 * @access public
	 * @return void
	 */
	public function __construct(Manager $spread_manager, ObjectManager $em, InterfaceProvider $provider)
	{
		$this->spread_manager = $spread_manager;
		$this->em             = $em;
		$this->provider       = $provider;
	}

	/**
	 * deploy
	 *
	 * @param TimelineAction $timeline_action
	 */
	public function deploy(TimelineAction $timeline_action)
	{
		$this->spread_manager->process($timeline_action);
		$results = $this->spread_manager->getResults();

		if($timeline_action->getStatusWanted() !== "published")
		{
			return;
		}

		foreach($results as $context => $values)
		{
			foreach($values as $entry)
			{
				$this->provider->add($timeline_action, $context, $entry->subject_model, $entry->subject_id);
			}
		}

		$timeline_action->setStatusCurrent(TimelineAction::STATUS_PUBLISHED);
        $timeline_action->setStatusWanted(TimelineAction::STATUS_FROZEN);

		$this->em->persist($timeline_action);
		$this->em->flush();

		// we have to clear results from spread manager
		$this->spread_manager->clear();
	}

	/**
	 * setDelivery
	 *
	 * @param mixed $delivery
	 * @access public
	 * @return void
	 */
	public function setDelivery($delivery)
	{
		$this->delivery = $delivery;
	}

	/**
	 * getDelivery
	 *
	 * @return string
	 */
	public function getDelivery()
	{
		return $this->delivery;
	}
}
