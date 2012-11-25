<?php

namespace Spy\TimelineBundle\Spread;

use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Spread\Entry\EntryCollection;

/**
 * How to define a spread
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface SpreadInterface
{
    /**
     * You spread class is support the action ?
     *
     * @param ActionInterface $action
     *
     * @return boolean
     */
    public function supports(ActionInterface $action);

    /**
     * @param  ActionInterface $action action we look for spreads
     * @param  EntryCollection $coll   Spreads defined on an EntryCollection
     * @return void
     */
    public function process(ActionInterface $action, EntryCollection $coll);
}
