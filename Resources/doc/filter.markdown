# Filters

Filters will apply modificaiton to collection of actions.

This bundle is provided with 2 filters, **DuplicateKey** and **DataHydrator**

## DuplicateKey

Imagine this use case:

    \Entity\User | 1 | friend | \Entity\User | 2
    \Entity\User | 2 | friend | \Entity\User | 1

You may not want to show on your page these two identicals actions. By this way, you have **duplicateKey** field.

When you'll create these two TimelineActions, define a same DuplicateKey .

After filtering with DuplicateKey filter, this will delete one of the two actions (the biggest duplicatePriority field, if you not define it, it will delete second entry).
It'll set to TRUE the **isDuplicated** field on timeline_action.

To use:

```yml
spy_timeline:
    #....
    filters:
        duplicate_key: ~
```

## DataHydrator

```
#Options available:
spy_timeline:
	filters:
		data_hydrator:
            priority:             20
            service:              spy_timeline.filter.data_hydrator
            filter_unresolved:    true
            locators:
                - spy_timeline.filter.data_hydrator.locator.doctrine
```

This filter will hydrate yours related object, this will regrouping the queries to avoid X queries call by action.
By this way, if you have two timelines:

    \Entity\User:1 | comment | \Entity\Article:2 | of | \Entity\User:2
    \Entity\User:2 | comment | \Entity\Article:7 | of | \Entity\User:1

It will execute 2 sql queries !

* \Entity\User    -> whereIn 1 and 2
* \Entity\Article -> whereIn 2 and 7

This actually work with doctrine ORM/ODM, **composite keys** are supported.

You can add your own locator, for example if you store yours components on a filesystem or an other storage.
BTW, you'll have to implements LocatorInterface.

### Removing Actions with Unresolved References
Use the `filter_unresolved: true` option to remove any actions which have unresolved references after the hydration process.
This will prevent unexpected `EntityNotFoundException`s when accessing an action component which have been removed
from the database, but are marked for Lazy-Loading by the entity loading listener.

## Adding a filter

Create the class and add it as a service:

```php
<?php
namespace Acme\DemoBundle\Filter;

use Spy\TimelineBundle\Filter\FilterInterface;
use Spy\TimelineBundle\Model\Collection;

class MyOwnFilter implements FilterInterface
{
	public function filter(Collection $collection)
	{
		// have fun
		return $results;
	}

    public function getPriority()
    {
        return 1337;
    }
}
```

Define this class as service and use tag `spy_timeline.filter`.
