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

````php
<?php
    use Highco\TimelineBundle\Timeline\Spread\SpreadInterface;
    use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;
    use Highco\TimelineBundle\Timeline\Spread\Entry\Entry;

    class MySpread implements SpreadInterface
    {
        public function supports(TimelineAction $timelineAction)
        {
            return true; //or false
        }

        public function process(TimelineAction $timelineAction, EntryCollection $coll)
        {
            $entry = new Entry();
            $entry->subjectModel = "\MySubject";
            $entry->subjectId = 1;

            $coll->set('mytimeline', $entry);
        }
    }
````

Add it to services


````xml
    <service id="my_service" class="MyClass">
        <tag name="highco.timeline.spread"/>
    </service>
````
