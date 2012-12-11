# Notification

During the deployment of an action, you can define some notifiers, they must implements **NotifierInterface**

**UnreadNotification** is already provided on this bundle.

How to use it ?

```yaml
#config.yml

spy_timeline:
     notifiers:
		 - spy_timeline.unread_notifications
```

```php
<?php
$subject = $actionManager->findOrCreateComponent('some\model', array(1, 2));

$unread = $this->get('spy_timeline.unread_notifications');
//count how many unread message for global context
$count  = $unread->countKeys($subject); // on global context
$count  = $unread->countKeys($subject, 'MyContext');

// remove ONE unread notification
$unread->markAsReadTimelineAction($subject, 'TimelineActionId'); // on global context
$unread->markAsReadTimelineAction($subject, 'TimelineActionId', 'MyContext');

// remove several unread notifications
$unread->markAsReadTimelineActions(array(
	array('GLOBAL', $subject, 'TimelineActionId'),
	array('GLOBAL', $subject, 'TimelineActionId'),
	...
));

// all unread notifications
$unread->markAllAsRead($subject); // on global context
$unread->markAllAsRead($subject, 'MyContext');

// retrieve timeline actions
$actions = $unread->getUnreadNotifications($subject); // on global context, no options
$actions = $unread->getUnreadNotifications($subject, 'MyContext', $options);
// in options you can define offset, limit, etc ...
```
