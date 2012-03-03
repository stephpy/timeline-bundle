<?php

namespace Highco\TimelineBundle\Twig\Extension;

use Highco\TimelineBundle\Entity\TimelineAction;

/**
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1(at)gmail.com>
 */
class TimelineExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var array
     */
    private $config;

    /**
     * @param \Twig_Environment $twig
     * @param array             $config
     */
    public function __construct(\Twig_Environment $twig, array $config)
    {
        $this->twig   = $twig;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'timeline_render' => new \Twig_Function_Method($this, 'renderTimeline', array('is_safe' => array('html'))),
        );
    }

    /**
     * @param TimelineAction  $timelineAction
     * @param string|null     $template
     *
     * @return string
     */
    public function renderTimeline(TimelineAction $timelineAction, $template = null)
    {
        if (null === $template) {
            $template = $this->getDefaultTemplate($timelineAction);
        }

        $parameters = array(
            'timeline' => $timelineAction,
        );

        try {
            return $this->twig->render($template, $parameters);
        } catch(\Exception $e){
            if (null !== $this->config['fallback']) {
                return $this->twig->render($this->config['fallback'], $parameters);
            }

            throw new $e;
        }
    }

    /**
     * Returns the default template name.
     *
     * @param TimelineAction $timelineAction
     *
     * @return string
     */
    public function getDefaultTemplate(TimelineAction $timelineAction)
    {
        return vsprintf('%s:%s.html.twig', array(
            $this->config['path'],
            strtolower($timelineAction->getVerb())
        ));
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'timeline_render';
    }
}
