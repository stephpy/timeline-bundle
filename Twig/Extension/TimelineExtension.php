<?php

namespace Highco\TimelineBundle\Twig\Extension;

use Highco\TimelineBundle\Entity\TimelineAction;

/**
 * TimelineExtension
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1(at)gmail.com>
 */
class TimelineExtension extends \Twig_Extension
{
    private $twig;
    private $config;

    /**
     * __construct
     *
     * @param \Twig_Environment $twig
     * @param array $config
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
            'timeline_render' => new \Twig_Function_Method($this, 'timeline_render', array('is_safe' => array('html'))),
        );
    }

    /**
     * timeline_render
     *
     * @param TimelineAction $timeline_action
     * @param string $template
     * @return void
     */
    public function timeline_render(TimelineAction $timeline_action, $template = null)
    {
        if(is_null($template))
        {
            $template = $this->getDefaultTemplate($timeline_action);
        }

        $parameters = array(
            'timeline' => $timeline_action,
        );

        try {
            return $this->twig->render($template, $parameters);
        } catch(\Exception $e){

            if(false === is_null($this->config['fallback']))
            {
                return $this->twig->render($this->config['fallback'], $parameters);
            }

            throw new $e;
        }
    }

    /**
     * getDefaultTemplate
     *
     * @param TimelineAction $timeline_action
     * @return void
     */
    public function getDefaultTemplate(TimelineAction $timeline_action)
    {
        return vsprintf('%s:%s.html.twig', array(
            $this->config['path'],
            \strtolower($timeline_action->getVerb())
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

    public function getTokenParsers()
    {
        return array();
    }
}
