# Pagination

Works with KnpPaginatorBundle

## Working with pager

```php
<?php

use Highco\TimelineBundle\Pager\TimelinePagerToken;

class Controller
{
    public function myAction()
    {
        $paginator  = $this->get('knp_paginator');
        $page       = $this->get('request')->get('page', 1);
        $maxPerPage = 10;

        // \User is the class of User and 1337 is id
        $notifications = $paginator->paginate(
            new TimelinePagerToken(TimelinePagerToken::SERVICE_NOTIFICATION, '\User', 1337),
            $page,
            $maxPerPage
        );

        // \User is the class of User and 1337 is id
        $timeline = $paginator->paginate(
            new TimelinePagerToken(TimelinePagerToken::SERVICE_TIMELINE, '\User', 1337),
            $page,
            $maxPerPage
        );

        $data = compact('notifications', 'timeline');

        // ... look at knp paginator bundle to see how it works
    }

}
```

