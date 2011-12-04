<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Provider\InterfaceProvider;

class LocalPuller implements InterfacePuller
{
	private $provider;

	/**
	 * __construct
	 *
	 * @param InterfaceProvider $provider
	 * @access public
	 * @return void
	 */
	public function __construct(InterfaceProvider $provider)
	{
		$this->provider = $provider;
	}

	/**
	 * pull
	 *
	 * @param mixed $type
	 * @param mixed $params
	 * @param array $options
	 * @return array
	 */
	public function pull($type, $params, $options = array())
	{
		switch($type)
		{
			case 'wall':
				$results = $this->provider->getWall($params, $options);
			break;
			case 'timeline':
				$results = $this->provider->getTimeline($params, $options);
			break;
			default:
				throw new \InvalidArgumentException('Unknown type on '.__CLASS__);
			break;
		}

		//@todo there, we can apply filters ...
		return $results;
	}
}
