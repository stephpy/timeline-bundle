<?php

namespace Highco\TimelineBundle\Entity;

use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Model\TimelineAction;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * TimelineActionManager
 *
 * @uses TimelineActionManagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineActionManager implements TimelineActionManagerInterface
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function updateTimelineAction(TimelineAction $timelineAction)
    {
        $this->em->persist($timelineAction);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getTimelineWithStatusPublished($limit = 10)
    {
        return $this->em
            ->getRepository('HighcoTimelineBundle:TimelineAction')
            ->createQueryBuilder('ta')
            ->where('ta.statusWanted = :statusWanted')
            ->setMaxResults($limit)
            ->setParameter('statusWanted', TimelineAction::STATUS_PUBLISHED)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritDoc}
     */
    public function getTimelineActionsForIds(array $ids)
    {
        if (empty($ids)) {
            return array();
        }

        $qb = $this->em->getRepository('HighcoTimelineBundle:TimelineAction')->createQueryBuilder('ta');

        $qb
            ->add('where', $qb->expr()->in('ta.id', '?1'))
            ->orderBy('ta.createdAt', 'DESC')
            ->setParameter(1, $ids);

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritDoc}
     * @todo, we should detach this from the TimelineActionManager, no sense to let it on this class
     */
    public function getTimelineResultsForModelAndOids($model, array $oids)
    {
        $repository = $this->em->getRepository($model);
        if (method_exists($repository, "getTimelineResultsForModelAndOids")) {

            return $repository->getTimelineResultsForModelAndOids($oids);
        } else {
            $qb = $this->em->createQueryBuilder();

            $qb
                ->select('r')
                ->from($model, 'r INDEX BY r.id')
                ->where($qb->expr()->in('r.id', $oids));

            return $qb->getQuery()->getResult();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getTimeline(array $params, array $options = array())
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
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
