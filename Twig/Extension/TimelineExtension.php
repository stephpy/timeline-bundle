<?php

namespace Spy\TimelineBundle\Twig\Extension;

use Spy\TimelineBundle\Entity\TimelineAction;
use Spy\TimelineBundle\Twig\TokenParser\TimelineActionThemeTokenParser;
use \Twig_TemplateInterface;

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
     * @var Twig_TemplateInterface
     */
    protected $template;

    /**
     * @var \SplObjectStorage
     */
    protected $blocks;

    /**
     * @var \SplObjectStorage
     */
    protected $themes;

    /**
     * @var array
     */
    protected $resources;

    /**
     * @var array
     */
    protected $varStack;

    /**
     * @param \Twig_Environment $twig   Twig environment
     * @param array             $config and array of configuration
     */
    public function __construct(\Twig_Environment $twig, array $config, array $resources)
    {
        $this->twig    = $twig;
        $this->config  = $config;
        $this->resources = $resources;
        $this->blocks = new \SplObjectStorage();
        $this->themes = new \SplObjectStorage();
        $this->varStack = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'timeline' => new \Twig_Function_Method($this, 'renderContextualTimeline', array('is_safe' => array('html'))),
            'timeline_render' => new \Twig_Function_Method($this, 'renderTimeline', array('is_safe' => array('html'))),
            'timeline_component_render' => new \Twig_Function_Method($this, 'renderTimelineActionComponent', array('is_safe' => array('html'))),
            'i18n_timeline_render' => new \Twig_Function_Method($this, 'renderLocalizedTimeline', array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenParsers()
    {
        return array(
            // {% timeline_action_theme timeline "Acme::components.html.twig" %}
            new TimelineActionThemeTokenParser(),
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
     * Return an array of variables from a timeline component
     * @param TimelineAction $timelineAction
     * @param string         $component
     *
     * @return array
     */
    public function getComponentVariables(TimelineAction $timelineAction, $component)
    {
        $componentName = ucfirst(
            preg_replace_callback(
                '#_([a-z])#',
                function ($v) {
                    return strtoupper($v[1]);
                },
                $component
            )
        );
        $methodBase = 'get' . $componentName;

        $values = array();
        $values['value'] = $timelineAction->$methodBase();

        if (in_array($component, array('subject', 'direct_complement', 'indirect_complement'))) {
            foreach (array('model', 'id', 'text') as $key) {
                if ('Subject' === $componentName && 'text' == $key) {
                    continue;
                }

                $method = $methodBase . ucfirst($key);
                $values[$key] = $timelineAction->$method();
            }
        }

        if (!empty($values['model'])) {
            $values['normalized_model'] = strtolower(str_replace('\\', '_', $values['model']));
        }

        return $values;
    }

    /**
     * Render an action component
     *
     * @param TimelineAction  $timelineAction  TimelineAction
     * @param string          $component       Component to render (subject, verb, direct_complement, indirect_complement)
     * @param array           $variables       Additional variables to pass to templates
     * @return string
     */
    public function renderTimelineActionComponent(TimelineAction $timelineAction, $component, array $variables = array())
    {
        if(!in_array($component, array('subject', 'direct_complement', 'indirect_complement', 'verb'))) {
            throw new \InvalidArgumentException("Invalid timeline action component '$component'");
        }

        if (null === $this->template) {
            $this->template = reset($this->resources);
            if (!$this->template instanceof \Twig_Template) {
                $this->template = $this->twig->loadTemplate($this->template);
            }
        }

        $componentVariables = $this->getComponentVariables($timelineAction, $component);
        $componentVariables['type'] = $component;
        $componentVariables['timelineAction'] = $timelineAction;

        $custom = false;
        if(!empty($componentVariables['model'])) {
            $custom = '_'.$componentVariables['normalized_model'];
        }

        $rendering = $custom.'_'.$component.'component';
        $blocks = $this->getBlocks($timelineAction);

        if (isset($this->varStack[$rendering])) {
            $typeIndex = $this->varStack[$rendering]['typeIndex'] - 1;
            $types = $this->varStack[$rendering]['types'];
            $this->varStack[$rendering]['variables'] = array_replace_recursive($componentVariables, $variables);
        } else {
            $types = array($component);
            if($custom) {
                $types[] = $custom.'_default';
                $types[] = $custom.'_'.$component;
            }
            $typeIndex = count($types) - 1;
            $this->varStack[$rendering] = array (
                'variables' => array_replace_recursive($componentVariables, $variables),
                'types'     => $types,
            );
        }

        do {
            $types[$typeIndex] .= '_component';

            if (isset($blocks[$types[$typeIndex]])) {

                $this->varStack[$rendering]['typeIndex'] = $typeIndex;

                // we do not call renderBlock here to avoid too many nested level calls (XDebug limits the level to 100 by default)
                ob_start();
                $this->template->displayBlock($types[$typeIndex], $this->varStack[$rendering]['variables'], $blocks);
                $html = ob_get_clean();

                unset($this->varStack[$rendering]);

                return $html;
            }
        } while (--$typeIndex >= 0);

        throw new \Exception(sprintf(
            'Unable to render the timeline action component as none of the following blocks exist: "%s".',
            implode('", "', array_reverse($types))
        ));
    }

    /**
     * Returns the blocks used to render the view.
     *
     * Templates are looked for in the configured resources
     *
     * @param TimelineAction $timelineAction
     *
     * @return array An array of Twig_TemplateInterface instances
     */
    protected function getBlocks(TimelineAction $timelineAction)
    {
        if (!$this->blocks->contains($timelineAction)) {

            $templates = $this->resources;

            if($this->themes->contains($timelineAction)) {
                $templates = array_merge($templates, $this->themes[$timelineAction]);
            }

            $blocks = array();
            foreach ($templates as $template) {
                if (!$template instanceof \Twig_Template) {
                    $template = $this->twig->loadTemplate($template);
                }
                $templateBlocks = array();
                do {
                    $templateBlocks = array_merge($template->getBlocks(), $templateBlocks);
                } while (false !== $template = $template->getParent(array()));
                $blocks = array_merge($blocks, $templateBlocks);
            }
            $this->blocks->attach($timelineAction, $blocks);
        }

        return $this->blocks[$timelineAction];
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
     * @param string|null    $context        Template context path
     * @param string         $format         Template format
     *
     * @return string
     */
    public function renderContextualTimeline(TimelineAction $timelineAction, $context = null, $format = 'html')
    {
        if (null === $context) {
            $template = $this->getDefaultTemplate($timelineAction);
        } else {
            $template = $this->getContextualTemplate($timelineAction, $context, $format);
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
     * Returns the contextualized template name.
     *
     * @param TimelineAction $timelineAction
     * @param string         $context
     * @param string         $format
     *
     * @return string
     */
    public function getContextualTemplate(TimelineAction $timelineAction, $context, $format)
    {
        return vsprintf('%s:%s/%s.%s.twig', array(
                    $this->config['path'],
                    $context,
                    strtolower($timelineAction->getVerb()),
                    $format
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

    /**
     * Store themes for a given timelineAction
     *
     * @param TimelineAction $timelineAction
     * @param array          $resources
     */
    public function setTheme(TimelineAction $timelineAction, array $resources)
    {
        $this->themes->attach($timelineAction, $resources);
        $this->blocks->detach($timelineAction);
    }
}
