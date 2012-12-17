<?php

namespace Spy\TimelineBundle\Driver\ORM\QueryBuilder;

use Spy\Timeline\Driver\QueryBuilder\Criteria\Operator;
use Spy\Timeline\Driver\QueryBuilder\Criteria\Asserter;

/**
 * OperatorVisitor
 *
 * @uses VisitorInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class OperatorVisitor implements VisitorInterface
{
    /**
     * @var string
     */
    protected $dql;

    /**
     * @var integer
     */
    protected $nbJoinsNeeded = 0;

    /**
     * @var integer
     */
    protected $aliasNumber = 1;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * {@inheritdoc}
     */
    public function visit($operator)
    {
        if (!$operator instanceof Operator) {
            throw new \Exception('OperatorVisitor accepts only Operator instance');
        }


        $asserterFields = $dqlParts = array();
        foreach ($operator->getCriterias() as $criteria) {
            if ($criteria instanceof Operator) {
                $visitor = new OperatorVisitor();
                $visitor->setAliasNumber($this->aliasNumber);
                $visitor->visit($criteria);

                $this->aliasNumber   += $visitor->getNbJoinsNeeded();
                $this->nbJoinsNeeded += $visitor->getNbJoinsNeeded();
            } elseif ($criteria instanceof Asserter) {

                $visitor = new AsserterVisitor();
                $visitor->visit($criteria);

                $nbJoinsNeeded = $visitor->getNbJoinsNeeded();

                if ($nbJoinsNeeded === 1) {

                    if ($this->nbJoinsNeeded == 0) {
                        $this->nbJoinsNeeded = 1;
                    }

                    $field = $criteria->getField();

                    if (in_array($field, $asserterFields)) {
                        $this->nbJoinsNeeded += 1;
                        $this->aliasNumber   += 1;
                    } else {
                        $asserterFields[] = $field;
                    }

                }
            } else {
                throw new \Exception('Not supported');
            }

            $visitor->setAliasNumber($this->aliasNumber);
            $dqlParts[] = $visitor->getDql();

            $this->parameters = array_merge($this->parameters, $visitor->getParameters());
        }

        $this->dql = sprintf('(%s)', implode(' '.$operator->getType().' ', $dqlParts));
    }

    /**
     * {@inheritdoc}
     */
    public function getDql()
    {
        return $this->dql;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbJoinsNeeded()
    {
        return $this->nbJoinsNeeded;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param integer $aliasNumber aliasNumber
     */
    public function setAliasNumber($aliasNumber)
    {
        $this->aliasNumber = (int) $aliasNumber;
    }

    /**
     * @return integer
     */
    public function getAliasNumber()
    {
        return $this->aliasNumber;
    }
}
