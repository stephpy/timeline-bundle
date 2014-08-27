<?php

namespace Spy\TimelineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Spy\Timeline\Model\Action as BaseAction;

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
