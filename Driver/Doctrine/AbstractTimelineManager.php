<?php

namespace Spy\TimelineBundle\Driver\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Model\TimelineInterface;
use Spy\Timeline\Pager\PagerInterface;

/**
 * AbstractTimelineManager
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class AbstractTimelineManager
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
     * @param ObjectManager  $objectManager objectManager
     * @param PagerInterface $pager         pager
     * @param string         $timelineClass timelineClass
     */
    public function __construct(ObjectManager $objectManager, PagerInterface $pager, $timelineClass)
    {
        $this->objectManager = $objectManager;
        $this->pager         = $pager;
        $this->timelineClass = $timelineClass;
    }

    /**
     * {@inheritdoc}
     */
    public function createAndPersist(ActionInterface $action, ComponentInterface $subject, $context = 'GLOBAL', $type = TimelineInterface::TYPE_TIMELINE)
    {
        $timeline = new $this->timelineClass();

        $timeline->setType($type);
        $timeline->setAction($action);
        $timeline->setContext($context);
        $timeline->setSubject($subject);

        $this->objectManager->persist($timeline);
    }
}
