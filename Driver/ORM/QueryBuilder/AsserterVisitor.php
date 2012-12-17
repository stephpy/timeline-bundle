<?php

namespace Spy\TimelineBundle\Driver\ORM\QueryBuilder;

use Spy\Timeline\Driver\QueryBuilder\Criteria\Asserter;

/**
 * AsserterVisitor
 *
 * @uses VisitorInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class AsserterVisitor implements VisitorInterface
{
    /**
     * @var Asserter
     */
    protected $asserter;

    /**
     * @var float
     */
    protected $aliasNumber = 1;

    /**
     * @var array
     */
    protected $parameters  = array();

    /**
     * {@inheritdoc}
     */
    public function visit($asserter)
    {
        if (!$asserter instanceof Asserter) {
            throw new \Exception('AsserterVisitor accepts only Asserter instance');
        }

        $this->asserter = $asserter;
    }

    /**
     * {@inheritdoc}
     */
    public function getDql()
    {
        $field                  = $this->getFieldDqlKey();
        $key                    = str_replace('.', '_', $field).uniqid();
        $this->parameters[$key] = $this->asserter->getValue();
        $operator               = $this->asserter->getOperator();

        switch ($operator) {
            case Asserter::ASSERTER_IN:
            case Asserter::ASSERTER_NOT_IN:
                return $field.' '.$operator.' (:'.$key.')';
                break;
            default:
                return $field.' '.$operator.' :'.$key;
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNbJoinsNeeded()
    {
        $field = $this->asserter->getField();

        return in_array(QueryBuilder::getFieldLocation($field), array(
            'actionComponent',
            'component'
        )) ? 1 : 0;
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
        $this->aliasNumber = $aliasNumber;
    }

    /**
     * @param string        $field  field
     * @param integer|empty $number number
     *
     * @return string
     */
    public function getFieldDqlKey()
    {
        $field = $this->asserter->getField();

        $fieldLocation = QueryBuilder::getFieldLocation($field);
        if (in_array($fieldLocation, array('actionComponent', 'component'))) {
            $fieldLocation .= $this->aliasNumber;
        }

        return sprintf('%s.%s', $fieldLocation, $field);
    }

}
