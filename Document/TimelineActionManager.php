<?php

namespace Highco\TimelineBundle\Document;

use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Model\TimelineActionInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * TimelineActionManager
 *
 * @uses TimelineActionManagerInterface
 * @author Chris Jones <leeked@gmail.com>
 */
class TimelineActionManager implements TimelineActionManagerInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var \Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param DocumentManager $dm
     * @param string $class
     */
    public function __construct(DocumentManager $dm, $class)
    {
        $this->dm         = $dm;
        $this->repository = $this->dm->getRepository($class);
        $this->class      = $this->dm->getClassMetadata($class)->name;
    }

    /**
     * Return actual document manager
     *
     * @return DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->dm;
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTimelineAction(TimelineActionInterface $timelineAction)
    {
        $this->dm->persist($timelineAction);
        $this->dm->flush($timelineAction, array('flush' => true));
    }

    /**
     * {@inheritdoc}
     */
    public function getTimelineWithStatusPublished($limit = 10)
    {
        return $this->getRepository()
            ->createQueryBuilder()
            ->field('statusWanted')->equals(TimelineAction::STATUS_PUBLISHED)
            ->limit($limit)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimelineActionsForIds(array $ids)
    {
        if (empty($ids)) {
            return array();
        }

        return $this->getRepository()
            ->createQueryBuilder()
            ->field('$id')->in($ids)
            ->orderBy('createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeline(array $params, array $options = array())
    {
        if (!isset($params['subjectModel']) || !isset($params['subjectId'])) {
            throw new \InvalidArgumentException('You have to define a "subjectModel" and a "subjectId" to pull data');
        }

        $offset = isset($options['offset']) ? $options['offset'] : 0;
        $limit  = isset($options['limit']) ? $options['limit'] : 10;
        $status = isset($options['status']) ? $options['status'] : 'published';

        $qb = $this->em->getRepository($this->timelineActionClass)->createQueryBuilder('ta');

        $qb
            ->where('ta.subjectModel = :subjectModel')
            ->andWhere('ta.subjectId = :subjectId')
            ->andWhere('ta.statusCurrent = :status')
            ->orderBy('ta.createdAt', 'DESC')
            ->setParameter('subjectModel', $params['subjectModel'])
            ->setParameter('subjectId', $params['subjectId'])
            ->setParameter('status', $status)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
