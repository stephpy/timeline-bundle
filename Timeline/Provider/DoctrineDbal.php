<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Doctrine\Common\Persistence\ObjectManager;
use Highco\TimelineBundle\Model\TimelineAction;

/**
 * @uses ProviderInterface
 * @uses EntityRetrieverInterface
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DoctrineDbal implements ProviderInterface, EntityRetrieverInterface
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var EntityRetrieverInterface
     */
    private $entityRetriever;

    /**
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function getWall(array $params, $options = array())
    {
        throw new \OutOfRangeException("This method is not available yet for DoctrineDbal");
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeline(array $params, $options = array())
    {
        if (!isset($params['subjectModel']) || !isset($params['subjectId'])) {
            throw new \InvalidArgumentException('You have to define a "subjectModel" and a "subjectId" to pull data');
        }

        $offset = isset($options['offset']) ? $options['offset'] : 0;
        $limit  = isset($options['limit']) ? $options['limit'] : 10;
        $status = isset($options['status']) ? $options['status'] : 'published';

        $qb = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')->createQueryBuilder('ta');

        $qb
            ->where('ta.subjectModel = :subjectModel')
            ->andWhere('ta.subjectId = :subjectId')
            ->andWhere('ta.statusCurrent = :status')
            ->orderBy('ta.createdAt', 'DESC')
            ->setParameter('subjectModel', $params['subjectModel'])
            ->setParameter('subjectId', $params['subjectId'])
            ->setParameter('status', $status)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function add(TimelineAction $timelineAction, $context, $subjectModel, $subjectId)
    {
        throw new \OutOfRangeException("This method is not available yet for DoctrineDbal");
    }

    /**
     * {@inheritDoc}
     */
    public function setEntityRetriever(EntityRetrieverInterface $entityRetriever = null)
    {
        $this->entityRetriever = $entityRetriever;
    }

    /**
     * {@inheritdoc}
     */
    public function find(array $ids)
    {
        if (empty($ids)) {
            return array();
        }

        $qb = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')->createQueryBuilder('ta');

        $qb
            ->add('where', $qb->expr()->in('ta.id', '?1'))
            ->orderBy('ta.createdAt', 'DESC')
            ->setParameter(1, $ids)
        ;

        return $qb->getQuery()->getResult();
    }
}
