<?php

namespace Highco\TimelineBundle\Provider;

use Doctrine\Common\Persistence\ObjectManager;
use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Model\TimelineActionManagerInterface;

/**
 * Abstract doctrine provider
 *
 * @uses ProviderInterface
 */
abstract class AbstractDoctrineProvider implements ProviderInterface
{

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var TimelineActionManagerInterface
     */
    protected $timelineActionManager;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $timelineClass;

    /**
     * @var array
     */
    protected $delayedQueries = array();

    /**
     * @param ObjectManager $manager
     *
     * @return AbstractDoctrineProvider Provides a fluent interface
     */
    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return ObjectManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param TimelineActionManagerInterface $timelineActionManager
     *
     * @return AbstractDoctrineProvider Provides a fluent interface
     */
    public function setTimelineActionManager($timelineActionManager)
    {
        $this->timelineActionManager = $timelineActionManager;

        return $this;
    }

    /**
     * @return TimelineActionManagerInterface
     */
    public function getTimelineActionManager()
    {
        return $this->timelineActionManager;
    }

    /**
     * @param array $options
     *
     * @return AbstractDoctrineProvider Provides a fluent interface
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $timelineClass
     *
     * @return AbstractDoctrineProvider Provides a fluent interface
     */
    public function setTimelineClass($timelineClass)
    {
        $this->timelineClass = $timelineClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimelineClass()
    {
        return $this->timelineClass;
    }

}
