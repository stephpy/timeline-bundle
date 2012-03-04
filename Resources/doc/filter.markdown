# Filters

## Adding a filter

Create the class and add it as a service:

    use Highco\TimelineBundle\Timeline\Filter\FilterInterface;

    MyOwnFilter implements FilterInterface
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
            - highco.timeline.filter.duplicate_key
            * your id service *
            - highco.timeline.filter.data_hydrator

The order on filters on config.yml is important, filters will be executed on this order.

## Filter "Duplicate Key"

Imagine this use case:

    \Entity\User | 1 | friend | \Entity\User | 2
    \Entity\User | 2 | friend | \Entity\User | 1

You may not want to show on your page these two identicals actions. By this way, you have **duplicateKey** field.

When you'll create these two TimelineActions, define a same DuplicateKey .

After filtering with DuplicateKey filter, this will delete one of the two actions (the biggest duplicatePriority field, if you not define it, it will delete second entry).
It'll set to TRUE the **isDuplicated** field on timeline_action.

## Filter "Data hydrator"

This filter will hydrate yours related object, this will regrouping the queries to avoid 3 queries call by timeline action.
By this way, if you have two timelines:

    \Entity\User | 1 | comment | \Entity\Article | 2 | of | \Entity\User | 2
    \Entity\User | 2 | comment | \Entity\Article | 7 | of | \Entity\User | 1

It will execute 2 sql queries !

* \Entity\User    -> whereIn 1 and 2
* \Entity\Article -> whereIn 2 and 7

This actually work with doctrine ORM, and the oid field should be an **id** field


