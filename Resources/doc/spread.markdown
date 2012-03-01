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

    use Highco\TimelineBundle\Timeline\Spread\InterfaceSpread;
    use Highco\TimelineBundle\Timeline\Spread\Entry\EntryCollection;
    use Highco\TimelineBundle\Timeline\Spread\Entry\Entry;

    class MySpread implements InterfaceSpread
    {
        public function supports(TimelineAction $timeline_action)
        {
            return true; //or false
        }

        public function process(TimelineAction $timeline_action, EntryCollection $coll)
        {
            $entry = new Entry();
            $entry->subject_model = "\MySubject";
            $entry->subject_id = 1;

            $coll->set('mytimeline', $entry);
        }
    }


Add it to services


    <service id="my_service" class="MyClass">
        <tag name="highco.timeline.spread"/>
    </service>

