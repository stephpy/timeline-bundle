<?php

namespace Spy\TimelineBundle\Driver\Doctrine\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * "MULTI_CONCAT" "(" StringPrimary "," StringPrimary "," ......")"
 */
class MultiConcatFunction extends FunctionNode
{
    protected $strings = array();

    /**
     * @override
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        $platform = $sqlWalker->getConnection()->getDatabasePlatform();
        $strings = array_map(function ($v) use ($sqlWalker) {
            return $sqlWalker->walkStringPrimary($v);
        }, $this->strings);

        return call_user_func_array(array($platform, 'getConcatExpression'), $strings);
    }

    /**
     * @override
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $exception = false;
        while (!$exception) {
            try {
                $this->strings[] = $parser->StringPrimary();
                $parser->match(Lexer::T_COMMA);
            } catch (\Exception $e) {
                $exception = true;
            }
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
