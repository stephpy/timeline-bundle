<?php

namespace Spy\TimelineBundle\Entity;

use Spy\Timeline\Model\Action as BaseAction;

/**
 * Action entity for Doctrine ORM.
 *
 * @uses BaseAction
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Action extends BaseAction
{
    /**
     * actionComponents has to be a doctrine common collection.
     */
    public function __construct()
    {
        parent::__construct();

        $this->actionComponents = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
