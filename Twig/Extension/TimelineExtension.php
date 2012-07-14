<?php

namespace Highco\TimelineBundle\Twig\Extension;

use Highco\TimelineBundle\Entity\TimelineAction;
use Symfony\Component\HttpFoundation\Session;

/**
 * Twig extension
 * "timeline_render" -> renders a timeline by getting the path of twig 
 * templates from config.
 * Then, calls PATH/VERB.html.twig
 * Or, Calls PATH/VERB.LOCALE.html.twig if render.using_locale is true
 *
 * @author Stephane PY <py.stephane1@gmail.com>
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
     * @var Symfony\Component\HttpFoundation\Session
     */
    private $session;

    /**
     * @param \Twig_Environment $twig   Twig environment
     * @param array             $config and array of configuration
     */
    public function __construct(\Twig_Environment $twig, Session $session, array $config)
    {
        $this->twig = $twig;
        $this->config = $config;
        $this->session = $session;
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
     * @param TimelineAction $timelineAction What TimelineAction to render
     * @param string|null    $template       Force template path
     *
     * @return string
     */
    public function renderTimeline(TimelineAction $timelineAction, $template = null)
    {
        if (null === $template) {
            $template = $this->config['using_locale'] !== true ? $this->getDefaultTemplate($timelineAction) : $this->getDefaultLocalizedTemplate($timelineAction);
        }

        $parameters = array(
            'timeline' => $timelineAction,
        );

        try {
            return $this->twig->render($template, $parameters);
        } catch (\Twig_Error_Loader $e) {
            if (null !== $this->config['fallback']) {
                return $this->twig->render($this->config['fallback'], $parameters);
            }

            throw $e;
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
     * Returns the default template name using locale.
     *
     * @param TimelineAction $timelineAction
     *
     * @return string
     */
    public function getDefaultLocalizedTemplate(TimelineAction $timelineAction)
    {
        return vsprintf('%s:%s.%s.html.twig', array(
            $this->config['path'],
            strtolower($timelineAction->getVerb()),
            $this->session->getLocale()
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
