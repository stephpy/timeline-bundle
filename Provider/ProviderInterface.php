<?php

namespace Highco\TimelineBundle\Provider;

use Highco\TimelineBundle\Model\TimelineAction;

/**
 * How to define a provider
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface ProviderInterface
{
    /**
     * @param array $params  Give here your subject Model, subject id, context
     * @param array $options offset, limit
     *
     * @return array
     */
    public function getWall(array $params, $options = array());

    /**
     * This action has to be flushed
     *
     * @param  TimelineAction $timelineAction The timeline action object
     * @param  string         $context        The context where you want to push
     * @param  string         $subjectModel   The class of subject
     * @param  string         $subjectId      The oid of subject
     * @param  array          $options        Array of options
     * @return void
     */
    public function persist(TimelineAction $timelineAction, $context, $subjectModel, $subjectId, array $options = array());

    /**
     * count how many keys are stored
     *
     * @param string $context      The context
     * @param string $subjectModel The class of subject
     * @param string $subjectId    The oid of subject
     * @param array  $options      Array of options
     *
     * @return integer
     */
    public function countKeys($context, $subjectModel, $subjectId, array $options = array());

    /**
     * remove key from storage
     * This action has to be flushed
     *
     * @param  string $context          The context
     * @param  string $subjectModel     The class of subject
     * @param  string $subjectId        The oid of subject
     * @param  string $timelineActionId The timeline action id
     * @param  array  $options          Array of options
     * @return void
     */
    public function remove($context, $subjectModel, $subjectId, $timelineActionId, array $options = array());

    /**
     * remove all keys from storage
     * This action has to be flushed
     *
     * @param  string $context      The context
     * @param  string $subjectModel The class of subject
     * @param  string $subjectId    The oid of subject
     * @param  array  $options      Array of options
     * @return void
     */
    public function removeAll($context, $subjectModel, $subjectId, array $options = array());

    /**
     * flush data persisted
     *
     * @return array
     */
    public function flush();
}
