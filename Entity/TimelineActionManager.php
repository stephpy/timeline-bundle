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
     * {@inheritedDoc}
     */
    public function updateTimelineAction(TimelineAction $timelineAction)
    {
        $this->em->persist($timelineAction);
        $this->em->flush();
    }

    /**
     * {@inheritedDoc}
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
     * {@inheritedDoc}
     */
    public function getTimelineResultsForModelAndOids($model, array $oids)
    {
        $repository = $this->em->getRepository($model);
        if (method_exists($repository, "getTimelineResultsForOIds")) {

            return $repository->getTimelineResultsForOIds($ids);
        } else {
            $qb = $this->em->createQueryBuilder();

            $qb
                ->select('r')
                ->from($model, 'r INDEX BY r.id')
                ->where($qb->expr()->in('r.id', $ids));

            return $qb->getQuery()->getResult();
        }
    }
}
