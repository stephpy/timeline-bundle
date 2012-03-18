<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Highco\TimelineBundle\Model\TimelineAction;

/**
 * How to define a provider
 *
 * @package HighcoTimelineBundle
 * @release 1.0.0
 * @author  Stephane PY <py.stephane1@gmail.com>
 */
interface ProviderInterface
{
    /**
     * @param array $params  Give here your subject Model, subject id, context
     * @param array $options offset, limit
     *
     * @return array
     */
    function getWall(array $params, $options = array());

    /**
     * @param array $params  Give here your subject Model, subject id, context
     * @param array $options offset, limit
     *
     * @return array
     */
    function getTimeline(array $params, $options = array());

    /**
     * @param TimelineAction $timelineAction The timeline action object
     * @param string         $context        The context where you want to push
     * @param string         $subjectModel   The class of subject
     * @param string         $subjectId      The oid of subject
     */
    function persist(TimelineAction $timelineAction, $context, $subjectModel, $subjectId);

    /**
     * flush data persisted
     *
     * @return array
     */
    function flush();

    /**
     * This methods will hydrate object by an entity retriever defined on configuration.
     *
     * @param EntityRetrieverInterface $entityRetriever
     */
    function setEntityRetriever(EntityRetrieverInterface $entityRetriever = null);
}
