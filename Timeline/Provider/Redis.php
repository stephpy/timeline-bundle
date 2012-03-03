<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Predis\Client;
use Highco\TimelineBundle\Model\TimelineAction;

/**
 * @uses InterfaceProvider
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Redis implements InterfaceProvider
{
    /**
     * @var Client
     */
    private $redis;
    
    /**
     * @var InterfaceEntityRetriever
     */
    private $entityRetriever;

    /**
     * @var string
     */
    protected static $key = "Timeline:%s:%s:%s";

    /**
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
        if (!isset($params['subject_model']) || !isset($params['subject_id'])) {
            throw new \InvalidArgumentException('You have to define a "subject_model" and a "subject_id" to pull data');
        }

        $context    = $params['context'] ? (string) $params['context'] : 'GLOBAL';
        $offset     = isset($options['offset']) ? $options['offset'] : 0;
        $limit      = isset($options['limit']) ? $options['limit'] : 10;
        $limit      = $limit - 1; //coz redis return one more ...

        $key        = $this->getKey($context, $params['subject_model'], $params['subject_id']);
        $results    = $this->redis->zRevRange($key, $offset, ($offset + $limit));

        if (null === $this->entityRetriever) {
            return $results;
        }

        return $this->entityRetriever->find($results);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeline(array $params, $options = array())
    {
        if (null === $this->entityRetriever || !$this->entityRetriever instanceof InterfaceProvider) {
            throw new \Exception('Redis cannot return a list of timeline action from storage, you have to give him the principal storage as entity retriever');
        }

        return $this->entityRetriever->getTimeline($params, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function add(TimelineAction $timelineAction, $context, $subjectModel, $subjectId)
    {
        $key = $this->getKey($context, $subjectModel, $subjectId);

        return $this->redis->zAdd($key, time(), $timelineAction->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function setEntityRetriever(InterfaceEntityRetriever $entityRetriever = null)
    {
        $this->entityRetriever = $entityRetriever;
    }

    /**
     * Returns the redis key.
     *
     * @param string $context
     * @param string $subjectModel
     * @param string $subjectId
     *
     * @return string
     */
    public function getKey($context, $subjectModel, $subjectId)
    {
        return sprintf(self::$key, $context, $subjectModel, $subjectId);
    }

    /**
     * @param Client $redis
     */
    public function setRedis(Client $redis)
    {
        $this->redis = $redis;
    }
}
