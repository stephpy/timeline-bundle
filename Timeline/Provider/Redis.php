<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Predis\Client;
use Doctrine\Common\Persistence\ObjectManager;
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
    private $em;

    protected static $key = "Timeline:%s:%s:%s";

    /**
     * __construct
     *
     * @param Client $redis
     */
    public function __construct(Client $redis, ObjectManager $em)
    {
        $this->setRedis($redis);
        $this->em    = $em;
    }

    /**
     * getWall
     *
     * @param array $params
     * @param array $options
     * @return array
     */
    public function getWall($params, $options = array())
    {
        if(false === isset($params['subject_model']) || false === isset($params['subject_id']))
            throw new \InvalidArgumentException('You have to define a "subject_model" and a "subject_id" to pull data');

        $context      = $params['context'] ? (string) $params['context'] : 'GLOBAL';
        $offset       = isset($options['offset']) ? $options['offset'] : 0;
        $limit        = isset($options['limit']) ? $options['limit'] : 10;

        $key          = $this->getKey($context, $params['subject_model'], $params['subject_id']);
        $results      = $this->redis->zRange($key, $offset, $limit);

        //if there is no results from REDIS, return an empty array
        if(empty($results))
        {
            return array();
        }

        $qb           = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('ta')
            ->orderBy('ta.created_at', 'DESC')
            ;

        return $qb->add('where', $qb->expr()->in('ta.id', '?1'))
            ->setParameter(1, $results)
            ->getQuery()
            ->getResult();
    }

    /**
     * getTimeline
     *
     * @param array $params
     * @param array $options
     * @return array
     */
    public function getTimeline($params, $options = array())
    {
        if(false === isset($params['subject_model']) || false === isset($params['subject_id']))
            throw new \InvalidArgumentException('You have to define a "subject_model" and a "subject_id" to pull data');

        $offset       = isset($options['offset']) ? $options['offset'] : 0;
        $limit        = isset($options['limit']) ? $options['limit'] : 10;
        $status       = isset($options['status']) ? $options['status'] : 'published';

        return $this->em->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('ta')
            ->where('ta.subject_model = :subject_model')
            ->andWhere('ta.subject_id = :subject_id')
            ->andWhere('ta.status_current = :status')
            ->orderBy('ta.created_at', 'DESC')
            ->setParameter('subject_model', $params['subject_model'])
            ->setParameter('subject_id', $params['subject_id'])
            ->setParameter('status', $status)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * add
     *
     * @param TimelineAction $timeline_action
     * @param string $context
     * @param string $subject_model
     * @param string  $subject_id
     * @return boolean
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
}
