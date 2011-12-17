<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Highco\TimelineBundle\Model\TimelineAction;

interface InterfaceProvider
{
    /**
     * getWall
     *
     * @param mixed $params
     * @param array $options
     * @access public
     * @return void
     */
	public function getWall($params, $options = array());

    /**
     * getTimeline
     *
     * @param mixed $params
     * @param array $options
     * @access public
     * @return void
     */
	public function getTimeline($params, $options = array());

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
	public function add(TimelineAction $timeline_action, $context, $subject_model, $subject_id);
}
