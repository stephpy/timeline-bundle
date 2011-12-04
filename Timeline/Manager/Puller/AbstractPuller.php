<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Provider\InterfaceProvider;

use Highco\TimelineBundle\Timeline\Filter\InterfaceFilter;

abstract class AbstractPuller
{
	protected $filters = array();

	/**
	 * addFilter
	 *
	 * @param InterfaceFilter $filter
	 * @param integer $priority
	 */
	public function addFilter(InterfaceFilter $filter, $priority = 255)
	{
		if(false === isset($this->filters[$priority]))
			$this->filters[$priority] = array();

		$this->filters[$priority][] = $filter;
	}

	/**
	 * allFilters
	 *
	 * @return array
	 */
	public function allFilters()
	{
		ksort($this->filters);
		$filters = array();

		foreach ($this->filters as $all)
		{
			$filters = array_merge($filters, $all);
		}
		return $filters;
	}

	/**
	 * filter
	 *
	 * @param mixed $results
	 * @access public
	 * @return void
	 */
	public function filter($results)
	{
		$filters = $this->allFilters();

		foreach($filters as $filter)
		{
			$results = $filter->filter($results);
		}

		return $results;
	}
}
