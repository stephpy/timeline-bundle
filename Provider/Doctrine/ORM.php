<?php

namespace Highco\TimelineBundle\Provider\Doctrine;

use Doctrine\ORM\EntityManager;
use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Provider\ProviderInterface;
use Highco\TimelineBundle\Model\TimelineInterface;

/**
 * Doctrine Provider
 */
class ORM implements ProviderInterface
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var TimelineActionManagerInterface
     */
    protected $timelineActionManager;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $timelineClass;

    /**
     * @var array
     */
    protected $delayedQueries = array();

    /**
     * @param EntityManager                  $em                    Doctrine Entity Manager
     * @param TimelineActionManagerInterface $timelineActionManager Manager for storage
     * @param array                          $options               An array of options
     */
    public function __construct(EntityManager $em, $timelineClass, TimelineActionManagerInterface $timelineActionManager, array $options = array()) {
        $this->setEm($em)
             ->setTimelineClass($timelineClass)
             ->setTimelineActionManager($timelineActionManager)
             ->setOptions($options);
    }

    /**
     * @param EntityManager $em
     *
     * @return ORM Provides a fluent interface
     */
    public function setEm($em)
    {
        $this->em = $em;

        return $this;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param TimelineActionManagerInterface $timelineActionManager
     *
     * @return ORM Provides a fluent interface
     */
    public function setTimelineActionManager($timelineActionManager)
    {
        $this->timelineActionManager = $timelineActionManager;

        return $this;
    }

    /**
     * @return TimelineActionManagerInterface
     */
    public function getTimelineActionManager()
    {
        return $this->timelineActionManager;
    }

    /**
     * @param array $options
     *
     * @return ORM Provides a fluent interface
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $timelineClass
     *
     * @return ORM Provides a fluent interface
     */
    public function setTimelineClass($timelineClass)
    {
        $this->timelineClass = $timelineClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimelineClass()
    {
        return $this->timelineClass;
    }

    protected function getBaseQueryBuilder($context, $subjectModel, $subjectId)
    {
        $qb = $this->getEm()->getRepository($this->getTimelineClass())->createQueryBuilder('t');

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

        if(empty($results)) {
            return $results;
        }

        $ids = array_map(function($row) { return $row['timelineActionId']; }, $results);

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
        $em = $this->getEm();

        $timeline = new $this->getTimelineClass();
        /* @var $timeline TimelineInterface */

        $timeline->setTimelineAction($timelineAction);
        $timeline->setContext($context);
        $timeline->setSubjectModel($subjectModel);
        $timeline->setSubjectId($subjectId);

        $em->persist($timeline);
        // $em->flush() performed in flush() method
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
        $em = $this->getEm();
        $qb = $this->getBaseQueryBuilder($context, $subjectModel, $subjectId);

        $qb->andWhere('t.timelineActionId = :timelineActionId')
           ->setMaxResults(1)
           ;

        $entity = $qb->getQuery()->getSingleResult();
        $em->remove($entity);
        // $em->flush() handled by flush() method
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
        $em = $this->getEm();
        try {
            $em->getConnection()->beginTransaction();

            if(!empty($this->delayedQueries)) {
                foreach($this->delayedQueries as $query) {
                    /* @var $query \Doctrine\ORM\Query */
                    $results[] = $query->execute();
                }
            }

            $em->flush();
            $em->getConnection()->commit();

            $this->delayedQueries = array();
        } catch (Exception $e) {
            if($em->getConnection()->isTransactionActive()) {
                $em->getConnection()->rollback();
            }
            $em->close();
            throw $e;
        }

        return $results;
    }

}
