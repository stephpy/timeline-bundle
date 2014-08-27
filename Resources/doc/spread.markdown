# Spread System

Example, we add action

    Chuck Norris Own the World with Vic Mc Key

We want to publish it on:

* Chuck Norris wall
* Tom wall
* Bazinga Wall
* Francky Vincent Wall

When you publish a timeline action, you can choose spreads by defining Subject Model and Subject Id.

## Defining a Spread class


Create the class:

```php
<?php

namespace Acme\TimelineBundle\Spread;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Spread\SpreadInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Spy\Timeline\Spread\Entry\Entry;
use Spy\Timeline\Spread\Entry\EntryUnaware;

class MySpread implements SpreadInterface
{
    public function supports(ActionInterface $action)
    {
        return true; //or false, you can look at timeline action to make your decision
    }

    public function process(ActionInterface $action, EntryCollection $coll)
    {
        // can define an Entry with a ComponentInterface as argument
        $coll->add(new Entry($action->getComponent('subject')));

        // or an EntryUnware, on these examples, we are not aware about components and
        // we don't want to retrieve them, let bundle do that for us.

        // composite key
        $coll->add(new EntryUnaware('model', array('1', '2')));
        $coll->add(new EntryUnaware('some\othermodel', 1));
        $coll->add(new EntryUnaware('othermodel', 'aodadoa'), 'CUSTOM_CONTEXT');
    }
}
```

Add it to services


```xml
<service id="my_service" class="Acme\TimelineBundle\Spread">
    <tag name="spy_timeline.spread"/>
</service>
```

To see which spreads are defined:

```
php app/console spy_timeline:spreads
```
