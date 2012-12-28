<?php

namespace Spy\TimelineBundle\Entity;

use Spy\Timeline\Model\Action as BaseAction;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Action entity for Doctrine ORM.
 *
 * @uses BaseAction
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Action extends BaseAction
{
    /**
     * actionComponents and timelines has to be a doctrine common collection.
     */
    public function __construct()
    {
        parent::__construct();

        $this->actionComponents = new ArrayCollection();
        $this->timelines        = new ArrayCollection();
    }
}
