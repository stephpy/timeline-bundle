<?php

namespace Highco\TimelineBundle\Twig\Node;

class TimelineActionThemeNode extends \Twig_Node
{
    public function __construct(\Twig_NodeInterface $timelineAction, \Twig_NodeInterface $resources, array $attributes = array(), $lineno = 0, $tag = null)
    {
        parent::__construct(array('timelineAction' => $timelineAction, 'resources' => $resources), $attributes, $lineno, $tag);
    }

    /**
     * @param \Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('echo $this->env->getExtension(\'timeline_render\')->setTheme(')
            ->subcompile($this->getNode('timelineAction'))
            ->raw(', array(')
        ;

        foreach($this->getNode('resources') as $resource) {
            $compiler->subcompile($resource)->raw(', ');
        }

        $compiler->raw("));\n");
    }

}