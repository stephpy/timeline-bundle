# Pagination

A pagination is used when call:

```php
$timelineManager->getTimeline($subject, array('page' => 1, 'max_per_page' => '10', 'paginate' => true));
$actionManager->getSubjectActions($subject, array('page' => 1, 'max_per_page' => '10', 'paginate' => false));
```

## 1) Using default paginator provided with driver

```yml
spy_timeline:
    paginator: ~
```

## 2) Using [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle)

```yml
spy_timeline:
    paginator: spy_timeline.paginator.knp
```

**If you want to use twig functions from knp.paginator.bundle, pass `timeline.iterator` in argument. Example:**

```twig
{{ knp_pagination_render(timeline.iterator) }}
```

## 3) Using your own paginator

Define a service, which implements `Spy\Timeline\ResultBuilder\Pager\PagerInterface`

```yml
spy_timeline:
    paginator: your_service
```
