<?php

namespace Spy\TimelineBundle\Document;

use Spy\Timeline\Model\Action as BaseAction;
use Spy\Timeline\Model\ComponentInterface;

/**
 * Action entity for Doctrine ODM.
 *
 * @uses BaseAction
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Action extends BaseAction
{
    protected $subject;

    /**
     * actionComponents has to be a doctrine common collection.
     */
    public function __construct()
    {
        parent::__construct();

        $this->actionComponents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @param string $type                 type
     * @param string $component            component
     * @param string $actionComponentClass actionComponentClass
     *
     * @return void
     */
    public function addComponent($type, $component, $actionComponentClass)
    {
        parent::addComponent($type, $component, $actionComponentClass);

        if ('subject' === $type && $component instanceof ComponentInterface) {
            // useful for actionManager->getSubjectActions()
            $this->subject = $this->getComponent('subject');
        }
    }
}
