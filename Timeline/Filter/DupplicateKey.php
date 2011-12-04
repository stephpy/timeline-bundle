<?php

namespace Highco\TimelineBundle\Timeline\Filter;

class DupplicateKey implements InterfaceFilter
{
	/**
	 * filter
	 *
	 * @param mixed $results
	 * @access public
	 * @return void
	 */
	public function filter($results)
	{
		if(false === $results instanceof \Traversable AND false === is_array($results))
			return;

		$dupplicateKeys = array();

		foreach($results as $key => $result)
		{
			if($result->hasDupplicateKey())
			{
				$currentKey      = $result->getDupplicateKey();
				$currentPriority = $result->getDupplicatePriority();

				if(array_key_exists($currentKey, $dupplicateKeys))
				{
					//actual entry has bigger priority
					if($currentPriority > $dupplicateKeys[$currentKey]['priority'])
					{
						$keyToDelete = $dupplicateKeys[$currentKey]['key'];

						$dupplicateKeys[$currentKey]['key'] = $key;
						$dupplicateKeys[$currentKey]['priority'] = $currentPriority;
						$dupplicateKeys[$currentKey]['dupplicated'] = true;
					}
					else
					{
						$keyToDelete = $key;
						$dupplicateKeys[$currentKey]['dupplicated'] = true;
					}

					unset($results[$keyToDelete]);
				}
				else
				{
					$dupplicateKeys[$currentKey] = array(
						'key' => $key,
						'priority' => $currentPriority,
						'dupplicated' => false,
					);
				}
			}
		}

		foreach($dupplicateKeys as $key => $values)
		{
			if($values['dupplicated'])
			{
				$results[$key]->setIsDupplicated(true);
			}
		}

		return $results;
	}
}
