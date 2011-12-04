<?php

namespace Highco\TimelineBundle\Timeline\Spread;

use Highco\TimelineBundle\Timeline\Token\Timeline;

interface InterfaceSpread
{
    public function getResults();
    public function supports(Timeline $token);
    public function process(Timeline $token);
}
