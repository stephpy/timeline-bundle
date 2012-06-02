HighcoTimelineBundle
====================

See for more informations:

- [installation](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/install.markdown)
- [filter](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/filter.markdown)
- [notification](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/notification.markdown)
- [provider](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/provider.markdown)
- [renderer](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/renderer.markdown)
- [spread](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/spread.markdown)
- [basic_example](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/basic_example.markdown)

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

Wall of a subject is all his actions + all actions of his **SPREADS** (cf spread.markdown)

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

```php
<?php
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

$entry = TimelineAction::create($chuckObject, 'Own', $worldObject, $vicMcKeyObject);

$manager = $this->get('highco.timeline.manager');
$manager->push($entry);
```

# Pull Wall of Subject

```php
<?php
$manager = $this->get('highco.timeline.manager');
$results = $manager->getWall('\Chuck', 1, 'GLOBAL');
//GLOBAL is the context wanted (GLOBAL is default)
```

# Pull Timeline of Subject

```php
<?php
$manager = $this->get('highco.timeline.manager');
$results = $manager->getTimeline('\Chuck', 1);
// There is no context to call here
```

# Delivery

- Immediate: When the TimelineAction is persisted on DB, it will deploy on spreads via the provider
- Wait: It will less the TimelineAction in "waiting" mode, you can deploy on spreads by the command or an other way.

# Full configuration

```yaml
highco_timeline:
    db_driver: orm # or redis
	timeline_action_class: Acme\YourBundle\Entity\TimelineAction #if db_driver = orm
    notifiers:
        - highco.timeline.unread_notifications
    timeline_action_manager: highco.timeline_action_manager.default
    filters:
        - highco.timeline.filter.duplicate_key # Filter duplicate keys
        - highco.timeline.filter.data_hydrator  # Hydrate data from doctrine to get TimelineAction instead of ID
    spread:
        on_me: true                             # Spread each action on subject too
        on_global_context: true                 # Spread automatically on global context
    provider: highco.timeline.provider.redis    # write your own
    delivery: immediate                         # wait
    render:
        path:     'AcmeBundle:Timeline'
        fallback: 'AcmeBundle:Timeline:default.html.twig'
```
