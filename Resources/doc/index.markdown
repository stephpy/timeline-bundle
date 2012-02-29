HighcoTimelineBundle
====================

# How it works ?

To have a timeline you have:

- **Subject**
- **Verb**
- **DirectComplement**
- **IndirectComplement**

Example:

    Chuck Norris  Own   the World with Vic Mc Key
    |  SUBJECT  | VERB |  Direct |     | INDIRECT |

There is two type of action list to retrieve:

##Timelines

Timeline of a subject is all his actions, as you can see when you look at the subject page.

## Walls

Wall of a subject is all his actions + all actions of his **SPREADS**

## Context

Imagine Chucknorris, he has 233 friends and follow 20 companies.

I we have one context, like facebook, his wall will return each actions of his friends and companies.

You can too use **Contexts** to filter timelines, for this example, we can have 3 contexts:

* GLOBAL: actions of his friends and companies
* FRIEND: actions of his friends
* COMPANIES: actions of his companies

You can define as many context as you want.
If you have only one context, you'll get each actions without can easily filter them to return only "OWN" actions or have only actions of friends of ChuckNorris

That's why we have a "Global" context, and you can easily add other contexts.

# Adding a timeline action

    $manager = $this->get('highco.timeline.manager');

    $entry = new TimelineAction();
    $entry->setSubjectModel('\Chuck');
    $entry->setSubjectId(1);
    $entry->setVerb('Own');

    ##
    $entry->setDirectComplementModel('\World');
    $entry->setDirectComplementId(1);
    OR
    $entry->setDirectComplementText('World');
    ##

    ##
    $entry->setIndirectComplementModel('\VicMcKey');
    $entry->setIndirectComplementId(1);
    OR
    $entry->setIndirectComplementText('Vic');
    ##

    # OR #

    $entry = new TimelineAction();
    $entry->create($chuckObject, 'Own', $worldObject, $vicMcKeyObject);

    $manager = $this->get('highco.timeline.manager');
    $manager->push($entry);

# Pull Wall of Subject

    $manager = $this->get('highco.timeline.manager');
    $results = $manager->getWall('\Chuck', 1, 'GLOBAL');
    //GLOBAL is the context wanted (GLOBAL is default)

# Pull Timeline of Subject

    $manager = $this->get('highco.timeline.manager');
    $results = $manager->getTimeline('\Chuck', 1);
    // There is no context to call here

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


# Filters

## Adding a filter

Create the class and add it as a service:

    use Highco\TimelineBundle\Timeline\Filter\InterfaceFilter;

    MyOwnFilter implements InterfaceFilter
    {
        public function filter($results)
        {
            // have fun
            return $results;
        }
    }

Then, you can add this filter to the list on config.yml

    highco_timeline:
        filters:
            - highco.timeline.filter.dupplicate_key
            * your id service *
            - highco.timeline.filter.data_hydrator

The order on filters on config.yml is important, filters will be executed on this order.

## Filter "Dupplicate Key"

Imagine this use case:

    \Entity\User | 1 | friend | \Entity\User | 2
    \Entity\User | 2 | friend | \Entity\User | 1

You may not want to show on your page these two identicals actions. By this way, you have **dupplicate_key** field.

When you'll create these two TimelineActions, define a same DupplicateKey .

After filtering with DupplicateKey filter, this will delete one of the two actions (the biggest dupplicate_priority field, if you not define it, it will delete second entry).
It'll set to TRUE the **is_dupplicated** field on Timeline_aciton.

## Filter "Data hydrator"

This filter will hydrate yours related object, this will regrouping the queries to avoid 3 queries call by timeline action.
By this way, if you have two timelines:

    \Entity\User | 1 | comment | \Entity\Article | 2 | of | \Entity\User | 2
    \Entity\User | 2 | comment | \Entity\Article | 7 | of | \Entity\User | 1

It will execute 2 sql queries !

* \Entity\User    -> whereIn 1 and 2
* \Entity\Article -> whereIn 2 and 7

This actually work with doctrine ORM, and the oid field should be an **id** field

# Providers

## Adding a provider

Create the class:

    use Highco\TimelineBundle\Timeline\Provider\InterfaceProvider;

    MyProvider implements InterfaceProvider
    {
        public function getWall($params, $options = array())
        {
            // ...
        }

        public function getTimeline($params, $options = array())
        {
            // ...
        }


        public function add(TimelineAction $timeline_action, $context, $subject_model, $subject_id)
        {
            // ...
        }

    }

Define this as a service, and replace on you config.yml:

    highco_timeline:
        provider: *your_service*

## Provider "REDIS"

Depend on SncRedis, this will use PRedis (not useful to have redis extension on your php)
**Redis > 1.1 is recquired on server**

# Renderer

Using twig


    # Define
    render:
        path:     'AcmeBundle:Timeline'
        fallback: 'AcmeBundle:Timeline:default.html.twig'

    {{ timeline_render(entry) }}
    # This will try to call "AcmeBundle:Timeline:**verb**.html.twig
    # If exception, it return the fallback defined on config

    {{ timeline_render(entry, "your template") }}
    # This will try to call "your template"
    # If exception, it return the fallback defined on config


# Delivery

- Immediate: When the TimelineAction is persisted on DB, it will deploy on spreads via the provider
- Wait: It will less the TimelineAction in "waiting" mode, you can deploy on spreads by the command or an other way.

# Full configuration

    highco_timeline:
        filters:
            - highco.timeline.filter.dupplicate_key # Filter dupplicate keys
            - highco.timeline.filter.data_hydrator  # Hydrate data from doctrine to get TimelineAction instead of ID
        spread:
            on_me: true                             # Spread each action on subject too
            on_global_context: true                 # Spread automatically on global context
        provider: highco.timeline.provider.redis    # write your own
        delivery: immediate                         # wait
        render:
            path:     'AcmeBundle:Timeline'
            fallback: 'AcmeBundle:Timeline:default.html.twig'

Todo
----

- Write tests !!!!!

Wishlist
--------

As soon as posible:

- Notification system !

May be ...

- Can use Doctrine ODM, Propel, etc ...
