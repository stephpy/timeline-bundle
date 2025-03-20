<?php

namespace Spy\TimelineBundle\Driver\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Model\TimelineInterface;
use Spy\Timeline\ResultBuilder\ResultBuilderInterface;

class AbstractTimelineManager
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
    protected $timelineClass;

    /**
     * @param ObjectManager          $objectManager objectManager
     * @param ResultBuilderInterface $resultBuilder resultBuilder
     * @param string                 $timelineClass timelineClass
     */
    public function __construct(EntityManager $objectManager, ResultBuilderInterface $resultBuilder, $timelineClass)
    {
        $this->objectManager = $objectManager;
        $this->resultBuilder = $resultBuilder;
        $this->timelineClass = $timelineClass;
    }

    public function createAndPersist(ActionInterface $action, ComponentInterface $subject, $context = 'GLOBAL', $type = TimelineInterface::TYPE_TIMELINE)
    {
        /** @var TimelineInterface $timeline */
        $timeline = new $this->timelineClass();

        $timeline
            ->setType($type)
            ->setAction($action)
            ->setContext($context)
            ->setSubject($subject)
        ;

        $this->objectManager->persist($timeline);
    }
}
