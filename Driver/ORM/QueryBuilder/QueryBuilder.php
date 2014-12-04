<?php

namespace Spy\TimelineBundle\Driver\ORM\QueryBuilder;

use Doctrine\ORM\Query\Expr;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Spy\Timeline\Driver\QueryBuilder\QueryBuilder as BaseQueryBuilder;
use Spy\Timeline\Driver\QueryBuilder\QueryBuilderFactory;
use Spy\Timeline\Driver\QueryBuilder\Criteria\Asserter;
use Spy\Timeline\Driver\QueryBuilder\Criteria\Operator;
use Doctrine\Common\Persistence\ObjectManager;
use Spy\Timeline\ResultBuilder\ResultBuilderInterface;
use Spy\TimelineBundle\Driver\ORM\QueryBuilder\Criteria\CriteriaCollection;

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
    protected $actionClass;

    /**
     * @param QueryBuilderFactory    $factory       factory
     * @param ObjectManager          $objectManager objectManager
     * @param ResultBuilderInterface $resultBuilder resultBuilder
     * @param string                 $actionClass   actionClass
     */
    public function __construct(QueryBuilderFactory $factory, ObjectManager $objectManager, ResultBuilderInterface $resultBuilder, $actionClass)
    {
        parent::__construct($factory);

        $this->objectManager = $objectManager;
        $this->resultBuilder = $resultBuilder;
        $this->actionClass   = $actionClass;
    }

    /**
     * Build the queryBuilder
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        $qb = $this->objectManager
            ->getRepository($this->actionClass)
            ->createQueryBuilder('action')
            ->select('action')
        ;

        if ($this->getSubjects()) {
            $this->filterSubjects($qb);
        }

        if (null != $this->criterias) {
            $this->filterCriterias($qb);
        }

        if (null != $this->sort) {
            list($field, $order) = $this->sort;
        } else {
            $field = 'createdAt';
            $order = 'DESC';
        }

        $qb->orderBy(
            sprintf('%s.%s', $this->getFieldLocation($field), $field), $order
        );

        return $qb;
    }

    /**
     * @param array $options pager, filter
     *
     * @return array|object
     */
    public function execute(array $options = array())
    {
        $qb      = $this->createQueryBuilder();

        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'filter'       => true,
            'paginate'     => false,
        ));

        $options = $resolver->resolve($options);

        return $this->resultBuilder->fetchResults($qb, $this->page, $this->maxPerPage, $options['filter'], $options['paginate']);
    }

    /**
     * @param  QueryBuilder $qb qb
     * @throws \Exception
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

        $qb
            ->innerJoin('action.timelines', 'timeline')
            ->andWhere('timeline.subject IN (:subjectIds)')
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

        $criteriaCollection = new CriteriaCollection();
        $visitor->visit($this->criterias, $criteriaCollection);

        $parameters = array();

        foreach ($criteriaCollection as $criteria) {
            $parameters  = array_merge($criteria->getParameters(), $parameters);
            $aliasNumber = $criteria->getAliasNumber();

            switch ($criteria->getLocation()) {
                case 'timeline':

                    $timelineKey        = sprintf('timeline%s', $aliasNumber);
                    $qb->leftJoin('action.timelines', $timelineKey, Expr\Join::WITH, $criteria->getDql());

                    break;
                case 'actionComponent':

                    $actionComponentKey = sprintf('actionComponent%s', $aliasNumber);
                    $qb->leftJoin('action.actionComponents', $actionComponentKey, Expr\Join::WITH, $criteria->getDql());

                    break;
                case 'component':

                    $actionComponentKey = sprintf('actionComponent%s', $aliasNumber);
                    $componentKey       = sprintf('component%s', $aliasNumber);
                    $qb
                        ->leftJoin('action.actionComponents', $actionComponentKey)
                        ->leftJoin(sprintf('%s.component', $actionComponentKey), $componentKey, Expr\Join::WITH, $criteria->getDql())
                    ;

                    break;
            }
        }

        $qb->andWhere($visitor->getDql());

        foreach ($parameters as $key => $value) {
            $qb->setParameter($key, $value);
        }
    }
}
