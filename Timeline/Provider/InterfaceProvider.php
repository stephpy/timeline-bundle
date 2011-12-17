<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Highco\TimelineBundle\Model\TimelineAction;

interface InterfaceProvider
{
    /**
     * getWall
     *
     * @param array $params
     * @param array $options
     * @return array
     */
	public function getWall($params, $options = array());

    /**
     * getTimeline
     *
     * @param array $params
     * @param array $options
     * @return array
     */
	public function getTimeline($params, $options = array());

    /**
     * add
     *
     * @param TimelineAction $timeline_action
     * @param string $context
     * @param string $subject_model
     * @param string $subject_id
     * @return boolean
     */
	public function add(TimelineAction $timeline_action, $context, $subject_model, $subject_id);
}
