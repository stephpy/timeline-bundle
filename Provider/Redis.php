<?php

namespace Highco\TimelineBundle\Provider;

use Predis\Client as PredisClient;
use Snc\RedisBundle\Client\Phpredis\Client as PhpredisClient;
use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Model\TimelineActionManagerInterface;

/**
 * Redis provider (using SncRedis)
 *
 * @uses ProviderInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Redis implements ProviderInterface
{
    /**
     * @var PredisClient|PhpredisClient Client
     */
    private $redis;

    /**
     * @var TimelineActionManagerInterface
     */
    private $timelineActionManager;

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
    protected static $timelineKey = "Timeline:%s:%s:%s";

    /**
     * @param PredisClient|PhpredisClient    $redis                 Redis client
     * @param TimelineActionManagerInterface $timelineActionManager Manager for storage
     * @param array                          $options               An array of options
     */
    public function __construct($redis, TimelineActionManagerInterface $timelineActionManager, array $options = array())
    {
        $this->setRedis($redis);
        $this->timelineActionManager = $timelineActionManager;
        $this->options = array_merge(array(
            'pipeline' => true,
        ), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getWall(array $params, $options = array())
    {
        if (!isset($params['subjectModel']) || !isset($params['subjectId'])) {
            throw new \InvalidArgumentException('You have to define a "subjectModel" and a "subjectId" to pull data');
        }

        $context    = isset($params['context']) ? (string) $params['context'] : 'GLOBAL';
        $offset     = isset($options['offset']) ? $options['offset'] : 0;
        $limit      = isset($options['limit']) ? $options['limit'] : 10;
        $limit      = $limit - 1; //coz redis return one more ...
        $redisKey   = isset($options['key']) ? $options['key'] : self::$timelineKey;

        $key        = $this->getKey($context, $params['subjectModel'], $params['subjectId'], $redisKey);
        $results    = $this->redis->zRevRange($key, $offset, ($offset + $limit));

        return $this->timelineActionManager->getTimelineActionsForIds($results);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(TimelineAction $timelineAction, $context, $subjectModel, $subjectId, array $options = array())
    {
        $options = array_merge(array(
            'key' => self::$timelineKey,
        ), $options);

        $key = $this->getKey($context, $subjectModel, $subjectId, $options['key']);

        $this->persistedDatas[] = array(
            'zAdd',
            $key,
            $timelineAction->getSpreadTime(),
            $timelineAction->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function countKeys($context, $subjectModel, $subjectId, array $options = array())
    {
        $options = array_merge(array(
            'key' => self::$timelineKey,
        ), $options);

        $key = $this->getKey($context, $subjectModel, $subjectId, $options['key']);

        return $this->redis->zCard($key);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($context, $subjectModel, $subjectId, $timelineActionId, array $options = array())
    {
        $options = array_merge(array(
            'key' => self::$timelineKey,
        ), $options);

        $key = $this->getKey($context, $subjectModel, $subjectId, $options['key']);

        $this->persistedDatas[] = array(
            'zRem',
            $key,
            $timelineActionId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll($context, $subjectModel, $subjectId, array $options = array())
    {
        $options = array_merge(array(
            'key' => self::$timelineKey,
        ), $options);

        $key = $this->getKey($context, $subjectModel, $subjectId, $options['key']);

        $this->persistedDatas[] = array(
            'del',
            $key,
        );
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
            switch ($persistData[0]) {
                case 'del':
                    $replies[] = $client->del($persistData[1]);
                    break;
                case 'zAdd':
                    $replies[] = $client->zAdd($persistData[1], $persistData[2], $persistData[3]);
                    break;
                case 'zRem':
                    $replies[] = $client->zRem($persistData[1], $persistData[2]);
                    break;
                default:
                    throw new \OutOfRangeException('This function is not supported');
                    break;
            }
        }

        if ($this->options['pipeline']) {
            //@todo see how to make agree predis or phpredis to use only one method...
            if ($client instanceof \Predis\Pipeline\PipelineContext) {
                return $client->execute();
            } else {
                return $client->exec();
            }
        }

        return $replies;
    }

    /**
     * Returns the redis key.
     *
     * @param string $context      context
     * @param string $subjectModel class of subject
     * @param string $subjectId    oid of subject
     * @param string $redisKey     the key for redis
     *
     * @return string
     */
    public function getKey($context, $subjectModel, $subjectId, $redisKey)
    {
        return sprintf($redisKey, $context, $subjectModel, $subjectId);
    }

    /**
     * @param PredisClient|PhpredisClient $redis
     */
    public function setRedis($redis)
    {
        if (!$redis instanceof PhpredisClient && !$redis instanceof PredisClient) {
            throw new \InvalidArgumentException('You have to give a PhpRedisClient or a PredisClient');
        }

        $this->redis = $redis;
    }
}
