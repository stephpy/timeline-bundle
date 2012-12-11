<?php

namespace Spy\TimelineBundle\Spread\Entry;

use Spy\Timeline\Model\ComponentInterface;

/**
 * EntryInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface EntryInterface
{
    /**
     * @return string
     */
    public function getIdent();

    /**
     * @return ComponentInterface
     */
    public function getSubject();
}
