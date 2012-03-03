<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Highco\TimelineBundle\Model\TimelineAction;

/**
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface InterfaceProvider
{
    /**
     * @param array $params //Give here your subject Model, subject id, context
     * @param array $options //offset, limit
     *
     * @return array
     */
    function getWall(array $params, $options = array());

    /**
     * @param array $params //Give here your subject Model, subject id, context
     * @param array $options //offset, limit
     *
     * @return array
     */
    function getTimeline(array $params, $options = array());

    /**
     * @param TimelineAction $timelineAction
     * @param string         $context
     * @param string         $subjectModel
     * @param string         $subjectId
     *
     * @return boolean
     */
    function add(TimelineAction $timelineAction, $context, $subjectModel, $subjectId);

    /**
     * This methods will hydrate object by an entity retriever defined on configuration.
     *
     * @param InterfaceEntityRetriever $entityRetriever
     */
    function setEntityRetriever(InterfaceEntityRetriever $entityRetriever = null);
}
