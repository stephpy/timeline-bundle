<?php

namespace Spy\TimelineBundle\Driver\ORM\QueryBuilder\Criteria;

use Spy\Timeline\Driver\QueryBuilder\Criteria\Asserter;
use Spy\TimelineBundle\Driver\ORM\QueryBuilder\QueryBuilder;

class CriteriaField
{
    /**
     * @var string
     */
    protected $location;

    /**
     * @var integer
     */
    protected $aliasNumber;

    /**
     * @var boolean
     */
    protected $isNeedJoin = false;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var string
     */
    protected $dql;

    /**
     * @param Asserter $asserter    asserter
     * @param integer  $aliasNumber aliasNumber
     */
    public function createFromAsserter(Asserter $asserter, $aliasNumber)
    {
        $this->aliasNumber = $aliasNumber;
        $field             = $asserter->getField();
        $this->location    = QueryBuilder::getFieldLocation($field);
        $this->isNeedJoin  = in_array($this->location, array('timeline', 'actionComponent', 'component'));
        $locationSql       = $this->isNeedJoin ? $this->location.$aliasNumber : $this->location;
        $sqlDefinition     = sprintf('%s.%s', $locationSql, $field);

        $parameterKey      = str_replace('.', '_', $sqlDefinition).uniqid();
        $this->parameters[$parameterKey] = $this->transformValue($asserter->getValue(), $field);

        $operator   = $asserter->getOperator();
        switch ($operator) {
            case Asserter::ASSERTER_IN:
            case Asserter::ASSERTER_NOT_IN:
                $this->dql = $sqlDefinition.' '.$operator.' (:'.$parameterKey.')';
                break;
            default:
                $this->dql = $sqlDefinition.' '.$operator.' :'.$parameterKey;
                break;
        }
    }

    /**
     * @param mixed  $value value
     * @param string $field field
     *
     * @return mixed
     */
    public function transformValue($value, $field)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        }

        if ('identifier' === $field) {
            if (is_scalar($value)) {
                $value = (string) $value;
            }

            $value = serialize($value); // identifier is a serialized field.
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return boolean
     */
    public function isNeedJoin()
    {
        return $this->isNeedJoin;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getDql()
    {
        return $this->dql;
    }

    /**
     * @return integer
     */
    public function getAliasNumber()
    {
        return $this->aliasNumber;
    }
}
