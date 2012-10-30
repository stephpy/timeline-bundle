<?php

namespace Highco\TimelineBundle\Provider\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Model\TimelineInterface;
use Highco\TimelineBundle\Provider\AbstractDoctrineProvider;

/**
 * Doctrine Provider
 */
class ORM extends AbstractDoctrineProvider
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param $context
     * @param $subjectModel
     * @param $subjectId
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getBaseQueryBuilder($context, $subjectModel, $subjectId)
    {
        $qb = $this->manager->getRepository($this->getTimelineClass())->createQueryBuilder('t');

        return $qb->where('t.subjectModel = :subjectModel')
            ->andWhere('t.subjectId = :subjectId')
            ->andWhere('t.context = :context')
            ->setParameters(
            array(
                'subjectModel' => $subjectModel,
                'subjectId'    => $subjectId,
                'context'      => $context,
            )
        );
    }

    /**
     * {@inheritDoc}
     * @throws \InvalidArgumentException
     */
    public function setManager($manager)
    {
        if(!$manager instanceof EntityManager) {
            throw new \InvalidArgumentException('Manager must be an instance of \Doctrine\ORM\EntityManager');
        }
        return parent::setManager($manager);
    }

    /**
     * {@inheritdoc}
     */
    public function getWall(array $params, $options = array())
    {
        if (!isset($params['subjectModel']) || !isset($params['subjectId'])) {
            throw new \InvalidArgumentException('You have to define a "subjectModel" and a "subjectId" to pull data');
        }

        $context = isset($options['context']) ? (string)$params['context'] : 'GLOBAL';
        $offset = isset($options['offset']) ? $options['offset'] : 0;
        $limit = isset($options['limit']) ? $options['limit'] : 10;

        $qb = $this->getBaseQueryBuilder($context, $params['subjectModel'], $params['subjectId']);

        $qb->select('t.timelineActionId')
            ->orderBy('ta.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        $results = $query->getScalarResult();

        if (empty($results)) {
            return $results;
        }

        $ids = array_map(
            function ($row) {
                return $row['timelineActionId'];
            },
            $results
        );

        return $this->getTimelineActionManager()->getTimelineActionsForIds($ids);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(
        TimelineAction $timelineAction,
        $context,
        $subjectModel,
        $subjectId,
        array $options = array()
    ) {
        $manager = $this->manager;

        $timeline = new $this->getTimelineClass();
        /* @var $timeline TimelineInterface */

        $timeline->setTimelineAction($timelineAction);
        $timeline->setContext($context);
        $timeline->setSubjectModel($subjectModel);
        $timeline->setSubjectId($subjectId);

        $manager->persist($timeline);
        // $manager->flush() performed in flush() method
    }

    /**
     * count how many keys are stored
     *
     * @param string $context      The context
     * @param string $subjectModel The class of subject
     * @param string $subjectId    The oid of subject
     * @param array  $options      Array of options
     *
     * @return integer
     */
    public function countKeys($context, $subjectModel, $subjectId, array $options = array())
    {
        $qb = $this->getBaseQueryBuilder($context, $subjectModel, $subjectId);
        $qb->select('COUNT(t)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * remove key from storage
     * This action has to be flushed
     *
     * @param  string $context          The context
     * @param  string $subjectModel     The class of subject
     * @param  string $subjectId        The oid of subject
     * @param  string $timelineActionId The timeline action id
     * @param  array  $options          Array of options
     *
     * @return void
     */
    public function remove($context, $subjectModel, $subjectId, $timelineActionId, array $options = array())
    {
        $manager = $this->manager;
        $qb = $this->getBaseQueryBuilder($context, $subjectModel, $subjectId);

        $qb->andWhere('t.timelineActionId = :timelineActionId')
            ->setMaxResults(1);

        $entity = $qb->getQuery()->getSingleResult();
        $manager->remove($entity);
        // $manager->flush() handled by flush() method
    }

    /**
     * remove all keys from storage
     * This action has to be flushed
     *
     * @param  string $context      The context
     * @param  string $subjectModel The class of subject
     * @param  string $subjectId    The oid of subject
     * @param  array  $options      Array of options
     *
     * @return void
     */
    public function removeAll($context, $subjectModel, $subjectId, array $options = array())
    {
        $qb = $this->getBaseQueryBuilder($context, $subjectModel, $subjectId);

        $qb->delete();

        $this->delayedQueries[] = $qb->getQuery();
        // Delay query until flush() is called.
    }

    /**
     * flush data persisted
     *
     * @return array
     */
    public function flush()
    {
        $results = array();
        $manager = $this->manager;
        try {
            $manager->getConnection()->beginTransaction();

            if (!empty($this->delayedQueries)) {
                foreach ($this->delayedQueries as $query) {
                    /* @var $query \Doctrine\ORM\Query */
                    $results[] = $query->execute();
                }
            }

            $manager->flush();
            $manager->getConnection()->commit();

            $this->delayedQueries = array();
        } catch (Exception $e) {
            if ($manager->getConnection()->isTransactionActive()) {
                $manager->getConnection()->rollback();
            }
            $manager->close();
            throw $e;
        }

        return $results;
    }
}
