<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Filter\InterfaceFilter;

/**
 * InterfacePullerFilterable
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface InterfacePullerFilterable
{
	public function addFilter(InterfaceFilter $filter);
	public function filter($results);
}
