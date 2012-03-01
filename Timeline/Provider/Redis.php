<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Predis\Client;
use Highco\TimelineBundle\Model\TimelineAction;

/**
 * Redis
 *
 * @uses InterfaceProvider
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Redis implements InterfaceProvider
{
    private $redis;
    private $entity_retriever;

    protected static $key = "Timeline:%s:%s:%s";

    /**
     * __construct
     *
     * @param Client $redis
     */
    public function __construct(Client $redis)
    {
        $this->setRedis($redis);
    }

    /**
     * {@inheritdoc}
     */
    public function getWall(array $params, $options = array())
    {
        if(false === isset($params['subject_model']) || false === isset($params['subject_id']))
            throw new \InvalidArgumentException('You have to define a "subject_model" and a "subject_id" to pull data');

        $context      = $params['context'] ? (string) $params['context'] : 'GLOBAL';
        $offset       = isset($options['offset']) ? $options['offset'] : 0;
        $limit        = isset($options['limit']) ? $options['limit'] : 10;
        $limit        = $limit - 1; //coz redis return one more ...

        $key          = $this->getKey($context, $params['subject_model'], $params['subject_id']);
        $results      = $this->redis->zRevRange($key, $offset, ($offset + $limit));

        if(null == $this->entity_retriever) {
            return $results;
        } else {
            return $this->entity_retriever->find($results);
        }
    }

    /**
     * getTimeline
     *
     * @param array $params
     * @param array $options
     * @access public
     * @return void
     */
    public function getTimeline(array $params, $options = array())
    {
        if(null == $this->entity_retriever || false === $this->entity_retriever instanceof InterfaceProvider)
        {
            throw new \Exception('Redis cannot return a list of timeline action from storage, you have to give him the principal storage as entity retriever');
        }

        return $this->entity_retriever->getTimeline($params, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function add(TimelineAction $timeline_action, $context, $subject_model, $subject_id)
    {
        $key = $this->getKey($context, $subject_model, $subject_id);
        return $this->redis->zAdd($key, time(), $timeline_action->getId());
    }

    /**
     * Return redis key
     *
     * @param string $context
     * @param string $subject_model
     * @param string $subject_id
     * @return string
     */
    public function getKey($context, $subject_model, $subject_id)
    {
        return sprintf(self::$key, $context, $subject_model, $subject_id);
    }

    /**
     * setRedis
     *
     * @param Client $redis
     * @access public
     * @return void
     */
    public function setRedis(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritDoc}
     */
    public function setEntityRetriever(InterfaceEntityRetriever $entity_retriever = null)
    {
        $this->entity_retriever = $entity_retriever;
    }
}
