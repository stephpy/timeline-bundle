# Notification

During the deployment of a timeline action, you can define some notifiers, they must implements **NotifierInterface**

**UnreadNotification** is already on bundle.

How to use it ?

````yaml
#config.yml

highco_timeline:
     notifiers:
		 - highco.timeline.unread_notifications
````

````php
<?php

$unread = $this->get('highco.timeline.unread_notifications');
//count how many unread message for global context
$count  = $unread->countKeys('GLOBAL', 'MySubject', 'MyId'));

// remove ONE unread notification
$unread->removeUnreadNotification('GLOBAL', 'MySubject', 'MyId', 'TimelineActionId');

// remove several unread notifications
$unread->removeUnreadNotifications(array(
	array('GLOBAL', 'MySubject', 'MyId', 'TimelineActionId'),
	array('GLOBAL', 'MySubject', 'MyId', 'TimelineActionId'),
	...
));

// retrieve timeline actions
$actions = $unread->getTimelineActions('GLOBAL', 'MySubject', 'MyId', $options);
// in options you can define offset, limit, etc ...

// apply filters ?
$puller = $this->get('highco.timeline.local.puller');
$actions = $puller->filter($actions);

````



