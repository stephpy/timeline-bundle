<?php

namespace Highco\TimelineBundle\Twig\Extension;

use Highco\TimelineBundle\Entity\TimelineAction;

/**
 * Twig extension
 *
 * "timeline_render" -> renders a timeline by getting the path of twig
 * templates from config. Then, calls PATH/VERB.html.twig
 *
 * "i18n_timeline_render" -> renders timeline using locale.
 * PATH/VERB.LOCALE.html.twig if file exists
 * then falls back to PATH/VERB.DEFAULT_LOCALE.html.twig ( if set in conf )
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
        $this->twig    = $twig;
        $this->config  = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'timeline_render' => new \Twig_Function_Method($this, 'renderTimeline', array('is_safe' => array('html'))),
            'i18n_timeline_render' => new \Twig_Function_Method($this, 'renderLocalizedTimeline', array('is_safe' => array('html'))),
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
     * @param TimelineAction $timelineAction What TimelineAction to render
     * @param string|null    $locale         Locale of the template
     *
     * @return string
     */
    public function renderLocalizedTimeline(TimelineAction $timelineAction, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->config['i18n_fallback'];
        }

        $template = $this->getDefaultLocalizedTemplate($timelineAction, $locale);

        $parameters = array(
            'timeline' => $timelineAction,
        );

        try {
            return $this->twig->render($template, $parameters);
        } catch (\Twig_Error_Loader $e) {

            if ($locale != $this->config['i18n_fallback'] && null !== $this->config['i18n_fallback']) {
                $fallbackTemplate = $this->getDefaultLocalizedTemplate($timelineAction, $this->config['i18n_fallback']);
                try {
                    return $this->twig->render($fallbackTemplate, $parameters);
                } catch (\Twig_Error_Loader $e) {
                    //Let's look at the default template
                }
            }

            if (null !== $this->config['fallback']) {
                return $this->twig->render($this->config['fallback'], $parameters);
            }

            throw $e;
        }
    }

    /**
     * Returns the default template name using locale.
     *
     * @param TimelineAction $timelineAction timeline action object
     * @param string         $locale         which locale
     *
     * @return string
     */
    public function getDefaultLocalizedTemplate(TimelineAction $timelineAction,  $locale)
    {
        return vsprintf('%s:%s.%s.html.twig', array(
            $this->config['path'],
            strtolower($timelineAction->getVerb()),
            $locale
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
