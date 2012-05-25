<?php
namespace Highco\TimelineBundle\Redis;

use Predis\Client as PredisClient;
use Snc\RedisBundle\Client\Phpredis\Client as PhpredisClient;
use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Model\TimelineActionInterface;

/**
 * TimelineActionManager
 *
 * @uses TimelineActionManagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineActionManager implements TimelineActionManagerInterface
{
    /**
     * @var PredisClient|PhpredisClient Client
     */
    private $redis;

    /**
     * @param PredisClient|PhpredisClient $redis Redis client
     */
    public function __construct($redis)
    {
        $this->setRedis($redis);
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

    /**
     * {@inheritDoc}
     */
    public function updateTimelineAction(TimelineActionInterface $timelineAction)
    {
        // there we have to serialize model and add on redis.
        exit('todo');
    }

    /**
     * {@inheritDoc}
     * @throw \Exception
     */
    public function getTimelineWithStatusPublished($limit = 10)
    {
        throw new \Exception('This method is not supported for redis db_driver');
    }

    /**
     * {@inheritDoc}
     */
    public function getTimelineActionsForIds(array $ids)
    {
        // there we have to serialize model and add on redis.
        exit('todo');
    }

    /**
     * {@inheritDoc}
     * @throw \Exception
     */
    public function getTimeline(array $params, array $options = array())
    {
        throw new \Exception('This method is not supported for redis db_driver');
    }
}
