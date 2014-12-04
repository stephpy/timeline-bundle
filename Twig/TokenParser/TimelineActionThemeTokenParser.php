<?php

namespace Spy\TimelineBundle\Twig\TokenParser;

use Spy\TimelineBundle\Twig\Node\TimelineActionThemeNode;

/**
 * Provides 'timeline_action_theme' tag
 */
class TimelineActionThemeTokenParser extends \Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param \Twig_Token $token A Twig_Token instance
     *
     * @return \Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(\Twig_Token $token)
    {
        $stream = $this->parser->getStream();

        $action = $this->parser->getExpressionParser()->parseExpression();

        $resources = array();
        do {
            $resources[] = $this->parser->getExpressionParser()->parseExpression();
        } while (!$stream->test(\Twig_Token::BLOCK_END_TYPE));

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new TimelineActionThemeNode(
            $action,
            new \Twig_Node($resources),
            array(),
            $token->getLine(),
            $this->getTag()
        );
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'timeline_action_theme';
    }
}
