# Pagination

A pagination is used when call:

```php
$timelineManager->getTimeline($subject, array('page' => 1, 'max_per_page' => '10'));
$actionManager->getSubjectActions($subject, array('page' => 1, 'max_per_page' => '10'));
```

## 1) Using Default paginator provided with driver

```yml
spy_timeline:
    paginator: ~
```

## 2) Using [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle)

```yml
spy_timeline:
    paginator: spy_timeline.paginator.knp
```

## 3) Using your own paginator

Define a service, which implements `Spy\TimelineBundle\Pager\PagerInterface`

```yml
spy_timeline:
    paginator: your_service
```
