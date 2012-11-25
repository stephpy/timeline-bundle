<?php

namespace Spy\TimelineBundle\Tools;

/**
 * Doctrine
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Doctrine
{
    /**
     * @param string $class class
     *
     * @return string
     */
    public static function unsetProxyClass($class)
    {
        $reflectionClass = new \ReflectionClass($class);
        if ($reflectionClass->implementsInterface('\Doctrine\ORM\Proxy\Proxy')) {
            $reflectionClass = $reflectionClass->getParentClass();
        }

        return $reflectionClass->getName();
    }
}
