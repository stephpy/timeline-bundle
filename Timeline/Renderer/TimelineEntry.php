<?php

namespace Highco\TimelineBundle\Entry\Renderer;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Highco\TimelineBundle\Timeline\Entry\TimelineEntry;

class Templating
{
    private $engine;
    protected $config;

    /**
     * __construct
     *
     * @param EngineInterface $engine
     * @access public
     * @return void
     */
    public function __construct(EngineInterface $engine, $config)
    {
        $this->engine = $engine;
        $this->config = $config;
    }

    /**
     * render
     *
     * @param TimelineEntry $entry
     * @access public
     * @return void
     */
    public function render(TimelineEntry $entry, array $parameters = array())
    {
        $parameters['entry'] = $entry;

        try {
            return $this->engine->render($this->getTemplate($entry), $parameters);
        } catch(\Exception $e){
            return $this->engine->render($this->config['fallback'], $parameters);
        }
    }

    /**
     * getTemplate
     *
     * @param TimelineEntry $entry
     * @access public
     * @return void
     */
    public function getTemplate(TimelineEntry $entry)
    {
        return vsprintf('%s:%s.html.%s', array(
            $this->config['path'],
            \strtolower($entry->get(TimelineEntry::FIELD_VERB)),
            $this->config['engine']
        ));
    }
}
