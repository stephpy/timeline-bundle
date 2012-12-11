SpyTimelineBundle
====================

See for more informations:

- [installation](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/install.markdown)
- [filter](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/filter.markdown)
- [notification](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/notification.markdown)
- [pagination](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/pagination.markdown)
- [renderer](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/renderer.markdown)
- [spread](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/spread.markdown)
- [basic_example](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/basic_example.markdown)

# How it works ?

To have a timeline you have:

- **Subject**
- **Verb**
- **Components** (directComplement, indirectComplement, etc...)

Example:

    Chuck Norris  Own   the World with Vic Mc Key
    |  SUBJECT  | VERB |  Direct |     | INDIRECT |

There is two type of action list to retrieve:

## Timeline

Wall of a subject is all his actions + all actions of his **SPREADS** (cf spread.markdown)

## SubjectAction

## Context

Imagine Chuck Norris, he has 233 friends and follow 20 companies.

If we have one context, like facebook, his wall will return each actions of his friends and companies.

You can too use **Contexts** to filter timelines, for example, we can have 3 contexts:

* GLOBAL: actions of his friends and companies
* FRIEND: actions of his friends
* COMPANIES: actions of his companies

You can define as many context as you want.
If you have only one context, you'll get each actions without being able to easily filter them to return only "OWN" actions or have only actions of friends of ChuckNorris

That's why we have a "GLOBAL" context, and you can easily add other contexts.

# Adding a timeline action

```php
$actionManager = $this->get('spy_timeline.action_manager');
$subject       = $actionManager->findOrCreateComponent('a\model', array(1, 2));

// cod here is an object. (you can add a component as example of subject)
// you can add as many components as you want. subject is mandatory !
$action = $actionManager->create($subject, 'verb', array('directComplement' => $cod));
$actionManager->updateAction($action);
```

# Pull Timeline of Subject

```php
$actionManager   = $this->get('spy_timeline.action_manager');
$timelineManager = $this->get('spy_timeline.timeline_manager');
$subject         = $actionManager->findOrCreateComponent('a\model', array(1, 2));

$timeline = $timelineManager->getTimeline($subject);
```

# Pull Subject actions

```php
$actionManager   = $this->get('spy_timeline.action_manager');
$subject         = $actionManager->findOrCreateComponent('a\model', array(1, 2));

$timeline = $actionManager->getSubjectActions($subject);
```

# Delivery

- Immediate: When the Action is persisted on DB, it will deploy on spreads via the provider
- Wait:      It will less the Action in "waiting" mode, you can deploy on spreads by the command or an other way.

# Full configuration

```yaml
spy_timeline:
    drivers: # define only one.
        orm:
            object_manager: ~   # doctrine.orm.entity_manager
            classes:
                timeline:         'Acme\YourBundle\Entity\Timeline'
                action:           'Acme\YourBundle\Entity\Action'
                component:        'Acme\YourBundle\Entity\Component'
                action_component: 'Acme\YourBundle\Entity\ActionComponent'
        # OR
        odm:
            object_manager: ~   # doctrine.odm.entity_manager
            classes:
                timeline:         'Acme\YourBundle\Document\Timeline'
                action:           'Acme\YourBundle\Document\Action'
                component:        'Acme\YourBundle\Document\Component'
                action_component: 'Acme\YourBundle\Document\ActionComponent'
        # OR
        redis:
            client:           ~ # snc_redis.default
            pipeline:         true
            prefix:           vlr_timeline
            classes:
                action:           'Spy\TimelineBundle\Model\Action'
                component:        'Spy\TimelineBundle\Model\Component'
                action_component: 'Spy\TimelineBundle\Model\ActionComponent'

    timeline_manager: ~ # use custom or let it.
    action_manager:   ~ #  use custom or let it.

    notifiers:
        - highco.timeline.unread_notifications

    # let empty if you want to use default paginator
    # or use your own.
    paginator: spy_timeline.paginator.knp

    filters:
        duplicate_key:
            service:              spy_timeline.filter.duplicate_key
            priority:             10
        data_hydrator:
            priority:             20
            service:              spy_timeline.filter.data_hydrator
            filter_unresolved:    true
            locators:
                - spy_timeline.filter.data_hydrator.locator.doctrine
    spread:
        on_subject: true          # Spread each action on subject too
        on_global_context: true   # Spread automatically on global context
        deployer: highco.timeline.spread.deployer
        batch_size: 50 # How many persist before flush operation.
        delivery: immediate
    render:
        path:     'AcmeBundle:Timeline'
        fallback: 'AcmeBundle:Timeline:default.html.twig'
        i18n: #Do you want to use i18n when rendering ? if not, remove this node.
            fallback: en
        resources: []    # Always prepends 'HighcoTimelineBundle:Action:components.html.twig'
```
