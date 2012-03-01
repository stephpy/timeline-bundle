<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Highco\TimelineBundle\Model\TimelineAction;

/**
 * InterfaceProvider
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface InterfaceProvider
{
    /**
     * getWall
     *
     * @param array $params //Give here your subject Model, subject id, context
     * @param array $options //offset, limit
     * @return array
     */
    public function getWall(array $params, $options = array());

    /**
     * getTimeline
     *
     * @param array $params //Give here your subject Model, subject id, context
     * @param array $options //offset, limit
     * @return array
     */
    public function getTimeline(array $params, $options = array());

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

    /**
     * This methods will hydrate object by an entity retriever defined on configuration
     *
     * @param InterfaceEntityRetriever $entity_retriever
     */
    public function setEntityRetriever(InterfaceEntityRetriever $entity_retriever = null);
}
