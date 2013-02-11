# Notification

During the deployment of an action, you can define some notifiers, they must implements **NotifierInterface**

```php
<?php

namespace Acme\Foo;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Notification\NotifierInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;

class MyNotifier implements NotifierInterface
{
    public function notify(ActionInterface $action, EntryCollection $entryCollection)
    {
        // By example, to deploy on each $action with verb OWN and Recipient (subject) is an User:
        if ($action->getVerb() !== 'OWN') {
            return;
        }

        foreach ($entryCollection as $context => $entries) {
            foreach ($entries as $entry) {
                if ($entry->getSubject()->getModel() == 'User') {
                    // entry->getSubject() has to be a ComponentInterface
                    // sure, you can use other components than components which are stored on entryCollection.
                    $this->timelineManager->createAndPersist($action, $entry->getSubject(), $context, 'notificationORSOMETHINGELSE');
                    // don't forget to inject timelineManager in this case ;)
                }
            }
        }

        $this->timelineManager->flush();

        // You can look at UnreadNotificationManager for example which duplicates timeline entries.
    }
}
```

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
$unread->markAsReadAction($subject, 'actionId'); // on global context
$unread->markAsReadAction($subject, 'actionId', 'MyContext');

// remove several unread notifications
$unread->markAsReadActions(array(
	array('GLOBAL', $subject, 'actionId'),
	array('GLOBAL', $subject, 'actionId'),
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
