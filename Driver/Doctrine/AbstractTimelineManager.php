<?php

namespace Spy\TimelineBundle\Driver\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Model\TimelineInterface;
use Spy\Timeline\Metadata;
use Spy\Timeline\ResultBuilder\ResultBuilderInterface;

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
     * @var ResultBuilderInterface
     */
    protected $resultBuilder;

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @param ObjectManager          $objectManager objectManager
     * @param ResultBuilderInterface $resultBuilder resultBuilder
     * @param Metadata               $metadata      metadata
     */
    public function __construct(ObjectManager $objectManager, ResultBuilderInterface $resultBuilder, Metadata $metadata)
    {
        $this->objectManager = $objectManager;
        $this->resultBuilder = $resultBuilder;
        $this->metadata      = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function createAndPersist(ActionInterface $action, ComponentInterface $subject, $context = 'GLOBAL', $type = TimelineInterface::TYPE_TIMELINE)
    {
        $timelineClass = $this->metadata->getClass('timeline');
        $timeline = new $timelineClass();

        $timeline->setType($type);
        $timeline->setAction($action);
        $timeline->setContext($context);
        $timeline->setSubject($subject);

        $this->objectManager->persist($timeline);
    }
}
