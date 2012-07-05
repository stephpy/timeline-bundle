# Pagination

Works with KnpPaginatorBundle

## Working with pager

```php
<?php

    use Highco\TimelineBundle\Pager\TimelinePagerToken;

    class Controller
    {
        public function MyAction()
        {
	        $paginator = $this->get('knp_paginator');

			// \User is the class of User and 1337 is id
			$notifications = $paginator->paginate(
				new TimelinePagerToken(TimelinePagerToken::SERVICE_NOTIFICATION, '\User', 1337),
				$this->get('request')->get('page', 1),
				$this->get('request')->get('max_per_page', 10)
			);

			// \User is the class of User and 1337 is id
			$timeline = $paginator->paginate(
				new TimelinePagerToken(TimelinePagerToken::SERVICE_TIMELINE, '\User', 1337),
				$this->get('request')->get('page', 1),
				$this->get('request')->get('max_per_page', 10)
			);

			$data = compact('notifications', 'timeline');

            // ... look at knp paginator bundle to see how it works
        }

    }
```

