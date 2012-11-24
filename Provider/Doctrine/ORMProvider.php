<?php

namespace Spy\TimelineBundle\Provider\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Spy\TimelineBundle\Model\TimelineAction;
use Spy\TimelineBundle\Entity\Timeline;
use Spy\TimelineBundle\Provider\AbstractDoctrineProvider;

/**
 * Doctrine ORM Provider
 */
class ORMProvider extends AbstractDoctrineProvider
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $delayedQueries = array();

    /**
     * @param $type
     * @param $context
     * @param $subjectModel
     * @param $subjectId
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getBaseQueryBuilder($type, $context, $subjectModel, $subjectId)
    {
        $qb = $this->manager->getRepository($this->getTimelineClass())->createQueryBuilder('t');

        return $qb->where('t.type = :type')
                  ->andWhere('t.context = :context')
                  ->andWhere('t.subjectModel = :subjectModel')
                  ->andWhere('t.subjectId = :subjectId')
                  ->setParameters(
                    array(
                        'type'         => $type,
                        'context'      => $context,
                        'subjectModel' => $subjectModel,
                        'subjectId'    => $subjectId,
                    )
                  );
    }

    /**
     * Determine the type value for storage
     *
     * @param array $options
     *
     * @return string
     */
    protected function getTypeFromOptions(array $options)
    {
        /*
         * @TODO: Currently this is the only way we have for determining the 'type' of the storage.
         * The current method of passing in a key formatting string doesn't make sense in ORM space, and how this info
         * is stored should really be determine within the provider. It may be more appropriate for provider-consumers
         * to pass a 'type' value ('spread', 'unread-notifier', etc) to the provider and let the provider determine
         * it's storage needs.
         */
        $type = null;

        if (array_key_exists('key', $options)) {
            list($keyPrefix,) = explode(':', $options['key'], 2);
            if (preg_match('#^Timeline(.+)#', $keyPrefix, $matches)) {
                $type = strtolower($matches[1]);
            }
        }

        return $type ? : 'spread';

    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setManager($manager)
    {
        if (!$manager instanceof EntityManager) {
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

        $type = $this->getTypeFromOptions($options);
        $context = isset($options['context']) ? (string)$params['context'] : 'GLOBAL';
        $offset = isset($options['offset']) ? $options['offset'] : 0;
        $limit = isset($options['limit']) ? $options['limit'] : 10;

        $qb = $this->getBaseQueryBuilder($type, $context, $params['subjectModel'], $params['subjectId']);

        $qb->addSelect('ta')
            ->leftJoin('t.timelineAction', 'ta')
            ->orderBy('ta.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $qb->getQuery();

        $results = $query->execute();

        if (empty($results)) {
            return $results;
        }

        // Extract actions
        return array_map(
            function ($timeline) {
                /* @var $timeline Timeline */
                return $timeline->getTimelineAction();
            },
            $results
        );

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

        $timelineClass = $this->getTimelineClass();
        $timeline = new $timelineClass;
        /* @var $timeline Timeline */

        $timeline->setType($this->getTypeFromOptions($options));
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
        $type = $this->getTypeFromOptions($options);
        $qb = $this->getBaseQueryBuilder($type, $context, $subjectModel, $subjectId);
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
        $type = $this->getTypeFromOptions($options);
        $qb = $this->getBaseQueryBuilder($type, $context, $subjectModel, $subjectId);

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
        $type = $this->getTypeFromOptions($options);
        $qb = $this->getBaseQueryBuilder($type, $context, $subjectModel, $subjectId);

        $qb->delete();

        // Delay query until flush() is called.
        $this->delayedQueries[] = $qb->getQuery();
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
        } catch (\Exception $e) {
            if ($manager->getConnection()->isTransactionActive()) {
                $manager->getConnection()->rollback();
            }
            $manager->close();
            throw $e;
        }

        return $results;
    }
}
