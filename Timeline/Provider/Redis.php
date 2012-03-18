<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Predis\Client;
use Highco\TimelineBundle\Model\TimelineAction;

/**
 * Redis provider (using SncRedis)
 *
 * @uses ProviderInterface
 * @package HighcoTimelineBundle
 * @release 1.0.0
 * @author  Stephane PY <py.stephane1@gmail.com>
 */
class Redis implements ProviderInterface
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * @var EntityRetrieverInterface
     */
    private $entityRetriever;

    /**
     * @var array
     */
    private $persistedDatas = array();

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var string
     */
    protected static $key = "Timeline:%s:%s:%s";

    /**
     * @param Client $redis
     * @param array  $options
     */
    public function __construct(Client $redis, array $options = array())
    {
        $this->setRedis($redis);
        $this->options = array_merge($options, array(
            'pipeline' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getWall(array $params, $options = array())
    {
        if (!isset($params['subjectModel']) || !isset($params['subjectId'])) {
            throw new \InvalidArgumentException('You have to define a "subjectModel" and a "subjectId" to pull data');
        }

        $context    = $params['context'] ? (string) $params['context'] : 'GLOBAL';
        $offset     = isset($options['offset']) ? $options['offset'] : 0;
        $limit      = isset($options['limit']) ? $options['limit'] : 10;
        $limit      = $limit - 1; //coz redis return one more ...

        $key        = $this->getKey($context, $params['subjectModel'], $params['subjectId']);
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
        if (null === $this->entityRetriever || !$this->entityRetriever instanceof ProviderInterface) {
            throw new \Exception('Redis cannot return a list of timeline action from storage, you have to give him the principal storage as entity retriever');
        }

        return $this->entityRetriever->getTimeline($params, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(TimelineAction $timelineAction, $context, $subjectModel, $subjectId)
    {
        $key = $this->getKey($context, $subjectModel, $subjectId);

        $this->persistedDatas[] = array($key, time(), $timelineAction->getId());
    }

    /**
     * Flush data persisted,
     * If pipeline option is set to TRUE, pipeline of predis client will be used
     *
     * @return array
     */
    public function flush()
    {
        if (empty($this->persistedDatas)) {
            return array();
        }

        $client  = $this->redis;
        $replies = array();

        if ($this->options['pipeline']) {
            $client = $this->redis->pipeline();
        }

        foreach ($this->persistedDatas as $persistData) {
            $replies[] = $client->zAdd($persistData[0], $persistData[1], $persistData[2]);
        }

        if ($this->options['pipeline']) {
            return $client->execute();
        }

        return $replies;
    }

    /**
     * {@inheritDoc}
     */
    public function setEntityRetriever(EntityRetrieverInterface $entityRetriever = null)
    {
        $this->entityRetriever = $entityRetriever;
    }

    /**
     * Returns the redis key.
     *
     * @param string $context      context
     * @param string $subjectModel class of subject
     * @param string $subjectId    oid of subject
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
