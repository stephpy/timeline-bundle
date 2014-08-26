<?php

namespace Spy\TimelineBundle\Driver\ORM\QueryBuilder\Criteria;

use Spy\Timeline\Driver\QueryBuilder\Criteria\Asserter;

class CriteriaCollection implements \IteratorAggregate
{
    /**
     * @var array
     */
    protected $criterias = array();

    /**
     * @var integer
     */
    protected $aliasNumber = 1;

    /**
     * @param Asserter $asserter asserter
     *
     * @return CriteriaField
     */
    public function addFromAsserter(Asserter $asserter)
    {
        $criteria = new CriteriaField();
        $criteria->createFromAsserter($asserter, $this->aliasNumber);

        if ($criteria->isNeedJoin()) {
            $this->aliasNumber++;
        }

        $this->criterias[] = $criteria;

        return $criteria;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->criterias);
    }
}
