<?php

namespace Highco\TimelineBundle\Provider\Doctrine;

use Doctrine\ORM\EntityManager;
use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Provider\ProviderInterface;

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
    protected $timelines = array();

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

        $qb = $this->getEm()->getRepository($this->getTimelineClass())->createQueryBuilder('t');


        $qb->select('t.timelineActionId')
            ->where('t.subjectModel = :subjectModel')
            ->andWhere('t.subjectId = :subjectId')
            ->andWhere('t.context = :context')
            ->orderBy('ta.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $qb->getQuery();
        $query->setParameters(array(
                'subjectModel' => $params['subjectModel'],
                'subjectId' => $params['subjectId'],
                'context' => $context
            ));

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
        $timelines[] = new $this->getTimelineClass()
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
        // TODO: Implement countKeys() method.
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
        // TODO: Implement remove() method.
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
        // TODO: Implement removeAll() method.
    }

    /**
     * flush data persisted
     *
     * @return array
     */
    public function flush()
    {
        // TODO: Implement flush() method.
    }

}
