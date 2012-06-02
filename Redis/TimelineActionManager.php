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
    CONST TIMELINE_ACTION_KEY = "timeline:action";

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
        if (null === $timelineAction->getId()) {
            $timelineAction->setId($this->getNextId());
        }

        $this->redis->hset(self::TIMELINE_ACTION_KEY, $timelineAction->getId(), serialize($timelineAction));
    }

    /**
     * {@inheritDoc}
     */
    public function getTimelineActionsForIds(array $ids)
    {
        if (empty($ids)) {
            return array();
        }

        $fromStorage = $this->redis->hmget(self::TIMELINE_ACTION_KEY, $ids);

        $results     = array();
        foreach ($fromStorage as $timelineAction) {
            $results[] = unserialize($timelineAction);
        }

        return $results;
    }

    /**
     * @return integer
     */
    protected function getNextId()
    {
        return ($this->redis->hlen(self::TIMELINE_ACTION_KEY) + 1);
    }

    /**
     * {@inheritDoc}
     * Not supported on Redis
     * @throw \Exception
     */
    public function getTimelineWithStatusPublished($limit = 10)
    {
        throw new \Exception('This method is not supported for redis db_driver');
    }

    /**
     * {@inheritDoc}
     * Not supported on Redis
     * @throw \Exception
     */
    public function getTimeline(array $params, array $options = array())
    {
        throw new \Exception('This method is not supported for redis db_driver');
    }
}
