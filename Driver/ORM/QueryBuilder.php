<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Spy\Timeline\Driver\QueryBuilder\QueryBuilder as BaseQueryBuilder;
use Spy\Timeline\Driver\QueryBuilder\QueryBuilderFactory;
use Spy\Timeline\Driver\QueryBuilder\Criteria\Asserter;
use Spy\Timeline\Driver\QueryBuilder\Criteria\CriteriaInterface;
use Spy\Timeline\Driver\QueryBuilder\Criteria\Operator;
use Doctrine\Common\Persistence\ObjectManager;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Pager\PagerInterface;

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
     * @var PagerInterface
     */
    protected $pager;

    /**
     * @var string
     */
    protected $timelineClass;

    /**
     * @var string
     */
    protected $actionClass;

    /**
     * @var string
     */
    protected $componentClass;

    /**
     * @var string
     */
    protected $actionComponentClass;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var array
     */
    protected $joins = array();

    /**
     * It begins to this number for aliases.
     *
     * @var integer
     */
    protected $aliasNumber = 3;

    /**
     * @param QueryBuilderFactory $factory              factory
     * @param ObjectManager       $objectManager        objectManager
     * @param PagerInterface      $pager                pager
     * @param string              $timelineClass        timelineClass
     * @param string              $actionClass          actionClass
     * @param string              $componentClass       componentClass
     * @param string              $actionComponentClass actionComponentClass
     */
    public function __construct(QueryBuilderFactory $factory, ObjectManager $objectManager, PagerInterface $pager, $timelineClass, $actionClass, $componentClass, $actionComponentClass)
    {
        parent::__construct($factory);

        $this->objectManager        = $objectManager;
        $this->pager                = $pager;
        $this->timelineClass        = $timelineClass;
        $this->actionClass          = $actionClass;
        $this->componentClass       = $componentClass;
        $this->actionComponentClass = $actionComponentClass;
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
            ->leftJoin('timeline.action', 'action')
            ->leftJoin('action.actionComponents', 'actionComponent')
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


        return $qb;
    }

    /**
     * @param boolean $filter filter
     *
     * @return PagerInterface
     */
    public function execute($filter = true)
    {
        $qb      = $this->createQueryBuilder();

        $results = $qb->getQuery()->getResult();

        $pager   = $this->pager->paginate($qb, $this->page, $this->maxPerPage);
        $results = $pager->getItems();

        $actions = array_map(
            function ($timeline) {
                return $timeline->getAction();
            },
            $results
        );

        $pager->setItems($actions);

        if ($filter) {
            return $pager->filter($pager);
        }

        return $pager;
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

        // WRONG, i have to request on timeline.subject

        $qb->innerJoin('a.actionComponents', 'ac2', Expr\Join::WITH, '(ac2.action = a AND ac2.component IN (:subjectIds) AND ac2.type = :subjectType)')
            ->setParameter('subjectIds', $ids)
            ->setParameter('subjectType', 'type')
            ;
    }

    /**
     * @param QueryBuilder $qb qb
     */
    protected function filterCriterias($qb)
    {
        if ($this->criterias instanceof Operator) {
            $method = 'buildOperator';
        } elseif ($this->criterias instanceof Asserter) {
            if ($this->isNeedJoin($this->criterias)) {
                $this->addNewJoins($qb);
            }

            $method = 'buildAsserter';
        }

        $where = $this->{$method}($qb, $this->criterias);

        $qb->where($where);

        foreach ($this->parameters as $key => $parameters) {
            foreach ($parameters as $index => $value) {
                $qb->setParameter(sprintf('%s_%s', $key, $index), $value);
            }
        }
    }

    /**
     * @param QueryBuilder $qb qb
     */
    protected function addNewJoins($qb)
    {
        if (in_array($this->aliasNumber, $this->joins)) {
            return false;
        }

        $actionComponentKey = sprintf('actionComponent%s', $this->aliasNumber);
        $componentKey       = sprintf('component%s', $this->aliasNumber);

        $qb
            ->leftJoin('action.actionComponents', $actionComponentKey)
            ->leftJoin(sprintf('%s.component', $actionComponentKey), $componentKey)
            ;

        $this->joins[] = $this->aliasNumber;
    }

    /**
     * @param QueryBuilder $qb          qb
     * @param Operator     $operator    operator
     * @param boolean      $disableJoin disableJoin
     *
     * @return string
     */
    public function buildOperator($qb, $operator, $disableJoin = false)
    {
        $joined  = false;
        $isOr    = $operator->getType() === 'OR';

        foreach ($operator->getCriterias() as $key => $criteria) {

            if ($isOr) {
                $this->addNewJoins($qb);
                $joined = true;
                $disableJoin = true;
            }

            if ($criteria instanceof Operator) {
                $values[] = $this->buildOperator($qb, $criteria, $disableJoin);
            } else {
                $values[] = $this->buildAsserter($qb, $criteria);

                if ($this->isNeedJoin($criteria) && !$disableJoin) {
                    $this->addNewJoins($qb);
                    $joined = true;
                }

            }
        }

        if ($joined) {
            $this->aliasNumber += 1;
        }

        return sprintf('(%s)', implode(' '.$operator->getType().' ', $values));
    }

    /**
     * @param QueryBuilder $qb       qb
     * @param Asserter      $asserter asserter
     *
     * @return void
     */
    public function buildAsserter($qb, $asserter)
    {
        $field    = $asserter->getField();
        $sqlField = $this->getFieldKey($field, $this->aliasNumber);
        $key      = str_replace('.', '_', $sqlField);

        $index = 0;
        if (isset($this->parameters[$key])) {
            $parameters = $this->parameters[$key];
            end($parameters);
            $index = (key($parameters) + 1);
        }

        $this->parameters[$key][$index] = $asserter->getValue();

        $parameterName = sprintf('%s_%s', $key, $index);

        // here we have ot supoprt other operators.
        return sprintf('%s = :%s', $sqlField, $parameterName);
    }

    /**
     * @param CriteriaInterface $criteria    criteria
     *
     * @return boolean
     */
    public function isNeedJoin(CriteriaInterface $criteria)
    {
        if (in_array($this->aliasNumber, $this->joins)) {
            return false;
        }

        if ($criteria instanceof Operator) {
            foreach ($criteria->getCriterias() as $criteria) {
                if ($this->isNeedJoin($criteria)) {
                    return true;
                }
            }
        } else {
            $field = $criteria->getField();
            return in_array($this->getFieldLocation($field), array('actionComponent', 'component'));
        }
    }

    /**
     * @param string        $field  field
     * @param integer|empty $number number
     *
     * @return string
     */
    public function getFieldKey($field, $number = '')
    {
        $fieldLocation = $this->getFieldLocation($field);
        if (in_array($fieldLocation, array('actionComponent', 'component'))) {
            $fieldLocation .= $number;
        }

        return sprintf('%s.%s', $fieldLocation, $field);
    }
}
