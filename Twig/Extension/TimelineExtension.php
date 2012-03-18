<?php

namespace Highco\TimelineBundle\Twig\Extension;

use Highco\TimelineBundle\Entity\TimelineAction;

/**
 * Twig extension
 * "timeline_render" -> render a timeline by get from your config
 * the path of twig templates. Then, call PATH/VERB.html.twig
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
     * @param \Twig_Environment $twig   Twig environment
     * @param array             $config and array of configuration
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
     * @param TimelineAction $timelineAction What TimelineAction to render
     * @param string|null    $template       Force template path
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
        } catch (\Exception $e) {
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
