# Basic example

A demo application (orm driver) is available [here](https://github.com/stephpy/timeline-app).
You can read this basic example and look at the application to make it work.

This example explains how to have a simple Timeline with `GLOBAL` context.

##Context:

`Chuck norris` just controls `the world`. All world residents have to be informed about that!


## First step

Add this to your controller:

```php
<?php
use Acme\YourBundle\Entity\TimelineAction
// or other DbDriver TimelineAction.

//.....

public function myAction()
{
    //......
    $actionManager = $this->get('spy_timeline.action_manager');
    $subject       = $actionManager->findOrCreateComponent('\User', 'chucknorris');
    $action        = $actionManager->create($subject, 'control', array('directComplement' => 'the world'));
    $actionManager->updateAction($action);
}
```

But at this moment there is no spread for this action, the timeline action will be stored on your `driver` but nobody will be informed about this.

## Second step

Define your Spread.

Define the service `Acme\MyBundle\Resource\config\services.xml`:
Look at [documentation](http://symfony.com/doc/current/book/service_container.html) to know how to do it.

```xml
<service id="my_spread" class="Acme\MyBundle\Spread\MySpread">
    <tag name="spy_timeline.spread"/>
</service>
```

Now, create the class `Acme\MyBundle\Spread\MySpread`

```php
<?php

namespace Acme\MyBundle\Spread;

use Spy\Timeline\Spread\SpreadInterface;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Spy\Timeline\Spread\Entry\EntryUnaware;

class MySpread implements SpreadInterface
{
    public function supports(ActionInterface $action)
    {
        // here you define what actions you want to support, you have to return a boolean.
        if ($action->getSubject()->getIdentifier() == "chucknorris") {
            return true;
        }

        return false;
    }

    public function process(ActionInterface $action, EntryCollection $coll)
    {
        // adding steven seagal to be informed

        $coll->add(new EntryUnaware('\User', 'steven seagal'));

        // get all other users
        $users = MyBestClass::MyBestMethodToGetNerds();

        foreach ($users as $user) {
            $coll->add(new EntryUnaware(get_class($user), $user->getId()));
        }
    }
}
```

## Third step

It's ok, now you can get timeline actions for each user

In your controller:

```php
<?php
public function myAction()
{
    $actionManager   = $this->get('spy_timeline.action_manager');
    $timelineManager = $this->get('spy_timeline.timeline_manager');
    $subject         = $actionManager->findOrCreateComponent('\User', 'steven seagal');

    $timeline = $timelineManager->getTimeline($subject);

    // count entries before filtering process.
    $count = $timelineManager->countKeys($subject);

    // count entries after filtering process.
    $count = count($timeline);

    return array('coll' => $timeline);
}
```

In your template .twig:

```twig
{% for action in coll %}
    {{ timeline_render(action) }}
    {# i18n ? #}
    {{ i18n_timeline_render(timeline, 'en') }}
{% endfor %}
```

If you need, custom vars can also be passed to template as 3rd argument:

```twig
{% for action in coll %}
    {{ timeline_render(action, null, { 'some_var': some_value }) }}
    {{ i18n_timeline_render(timeline, 'en', { 'some_var': some_value }) }}
{% endfor %}

```
Look at [renderer](https://github.com/stephpy/timeline-bundle/blob/master/Resources/doc/renderer.markdown) to see how to define a path to store verbs.

If you have any questions, feel free to create an issue or contact us.
