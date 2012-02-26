<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Predis\Client;
use Doctrine\Common\Persistence\ObjectManager;
use Highco\TimelineBundle\Model\TimelineAction;

/**
 * DoctrineDbal
 *
 * @uses InterfaceProvider
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DoctrineDbal implements InterfaceProvider
{
    private $em;

    /**
     * __construct
     *
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em    = $em;
    }

    /**
     * getWall
     *
     * @param array $params
     * @param array $options
     * @return array
     */
    public function getWall(array $params, $options = array())
    {
        throw new \OutOfRangeException("This method is not available yet for DoctrineDbal");
    }

    /**
     * getTimeline
     *
     * @param array $params
     * @param array $options
     * @return array
     */
    public function getTimeline(array $params, $options = array())
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
        throw new \OutOfRangeException("This method is not available yet for DoctrineDbal");
    }
}
