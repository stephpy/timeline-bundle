<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Predis\Client;
use Highco\TimelineBundle\Model\TimelineAction;

class Redis implements InterfaceProvider
{
	private $redis;

	protected static $key = "Timeline:%s:%s:%s";

	/**
	 * __construct
	 *
	 * @param Client $redis
	 * @access public
	 * @return void
	 */
	public function __construct(Client $redis)
	{
		$this->redis = $redis;
	}

	/**
	 * add
	 *
	 * @param TimelineAction $timeline_action
	 * @param mixed $context
	 * @param mixed $subject_model
	 * @param mixed $subject_id
	 * @access public
	 * @return void
	 */
	public function add(TimelineAction $timeline_action, $context, $subject_model, $subject_id)
	{
		$key = $this->getKey($context, $subject_model, $subject_id);
		return $this->redis->zAdd($key, time(), $timeline_action->getId());
	}

	/**
	 * getKey
	 *
	 * @param mixed $context
	 * @param mixed $subject_model
	 * @param mixed $subject_id
	 * @access public
	 * @return void
	 */
	public function getKey($context, $subject_model, $subject_id)
	{
		return sprintf(self::$key, $context, $subject_model, $subject_id);
	}
}
