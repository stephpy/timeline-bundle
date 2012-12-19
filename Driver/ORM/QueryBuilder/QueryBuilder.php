<?php

namespace Spy\TimelineBundle\Driver\ORM\QueryBuilder;

use Spy\Timeline\Driver\QueryBuilder\QueryBuilder as BaseQueryBuilder;
use Spy\Timeline\Driver\QueryBuilder\QueryBuilderFactory;
use Spy\Timeline\Driver\QueryBuilder\Criteria\Asserter;
use Spy\Timeline\Driver\QueryBuilder\Criteria\CriteriaInterface;
use Spy\Timeline\Driver\QueryBuilder\Criteria\Operator;
use Doctrine\Common\Persistence\ObjectManager;
use Spy\Timeline\ResultBuilder\ResultBuilderInterface;
use Spy\Timeline\Model\ActionInterface;

/**
 * QueryBuilder
 *
 * @uses BaseQueryBuilder
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class QueryBuilder extends BaseQueryBuilder
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ResultBuilderInterface
     */
    protected $resultBuilder;

    /**
     * @var string
     */
    protected $timelineClass;

    CONST APPLY_FILTER     = true;
    CONST NOT_APPLY_FILTER = false;

    CONST PAGINATE         = true;
    CONST NOT_PAGINATE     = false;

    /**
     * @param QueryBuilderFactory    $factory       factory
     * @param ObjectManager          $objectManager objectManager
     * @param ResultBuilderInterface $resultBuilder resultBuilder
     * @param string                 $timelineClass timelineClass
     */
    public function __construct(QueryBuilderFactory $factory, ObjectManager $objectManager, ResultBuilderInterface $resultBuilder, $timelineClass)
    {
        parent::__construct($factory);

        $this->objectManager = $objectManager;
        $this->resultBuilder = $resultBuilder;
        $this->timelineClass = $timelineClass;
    }

    /**
     * Build the queryBuilder
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        $qb = $this->objectManager
            ->getRepository($this->timelineClass)
            ->createQueryBuilder('timeline')
            ->select('timeline, action, actionComponent, component')
            ->innerJoin('timeline.action', 'action')
            ->innerJoin('action.actionComponents', 'actionComponent')
            ->leftJoin('actionComponent.component', 'component')
            ;

        if ($this->getSubjects()) {
            $this->filterSubjects($qb);
        }

        if (null != $this->criterias) {
            $this->filterCriterias($qb);
        }

        if (null != $this->sort) {
            list ($field, $order) = $this->sort;
            $qb->orderBy($this->getFieldKey($field), $order);
        }

        if ($this->groupByAction) {
            $qb->groupBy('action.id, actionComponent.id, component.id');
        }

        return $qb;
    }

    /**
     * @param boolean $filter filter
     *
     * @return PagerInterface
     */
    public function execute($filter = self::APPLY_FILTER, $paginate = self::PAGINATE)
    {
        $qb      = $this->createQueryBuilder();

        return $this->resultBuilder->fetchResults($qb, $this->page, $this->maxPerPage, $filter, $paginate);
    }

    /**
     * @param QueryBuilder $qb qb
     */
    protected function filterSubjects($qb)
    {
        $ids = array();
        foreach ($this->getSubjects() as $component) {
            $id = $component->getId();
            if (empty($id)) {
                throw new \Exception('Component has to be fetched from database');
            }

            $ids[] = $id;
        }

        $qb->andWhere('timeline.subject IN (:subjectIds)')
            ->setParameter('subjectIds', $ids)
            ;
    }

    /**
     * @param QueryBuilder $qb qb
     */
    protected function filterCriterias($qb)
    {
        if ($this->criterias instanceof Operator) {
            $visitor = new OperatorVisitor();
        } elseif ($this->criterias instanceof Asserter) {
            $visitor = new AsserterVisitor();
        }

        $visitor->visit($this->criterias);

        for ($i = 1; $i < ($visitor->getNbJoinsNeeded() + 1); $i++) {

            $actionComponentKey = sprintf('actionComponent%s', $i);
            $componentKey       = sprintf('component%s', $i);

            $qb
                ->leftJoin('action.actionComponents', $actionComponentKey)
                ->leftJoin(sprintf('%s.component', $actionComponentKey), $componentKey)
                ;
        }

        $qb->andWhere($visitor->getDql());

        foreach ($visitor->getParameters() as $key => $value) {
            $qb->setParameter($key, $value);
        }
    }
}
