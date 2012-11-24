# Notification

During the deployment of a timeline action, you can define some notifiers, they must implements **NotifierInterface**

**UnreadNotification** is already on bundle.

How to use it ?

```yaml
#config.yml

spy_timeline:
     notifiers:
		 - spy_timeline.unread_notifications
```

```php
<?php

$unread = $this->get('spy_timeline.unread_notifications');
//count how many unread message for global context
$count  = $unread->countKeys('MySubject', 'MyId'); // on global context
$count  = $unread->countKeys('MySubject', 'MyId', 'MyContext');

// remove ONE unread notification
$unread->markAsReadTimelineAction('MySubject', 'MyId', 'TimelineActionId'); // on global context
$unread->markAsReadTimelineAction('MySubject', 'MyId', 'TimelineActionId', 'MyContext');

// remove several unread notifications
$unread->markAsReadTimelineActions(array(
	array('GLOBAL', 'MySubject', 'MyId', 'TimelineActionId'),
	array('GLOBAL', 'MySubject', 'MyId', 'TimelineActionId'),
	...
));

// all unread notifications
$unread->markAllAsRead('MySubject', 'MyId'); // on global context
$unread->markAllAsRead('MySubject', 'MyId', 'MyContext');

// retrieve timeline actions
$actions = $unread->getTimelineActions('MySubject', 'MyId'); // on global context, no options
$actions = $unread->getTimelineActions('MySubject', 'MyId', 'MyContext', $options);
// in options you can define offset, limit, etc ...

// apply filters ?
$actions = $this->get('spy_timeline.manager')->applyFilters($actions);
```
