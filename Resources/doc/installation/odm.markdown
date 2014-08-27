ODM Driver
==========

You'll have to define documents on a bundle, you can add it to anyone bundle, in this example will use `Yo\UrBundle`.

# 1) Define configuration

```yml
#config.yml
spy_timeline:
    drivers:
        odm:
            object_manager: doctrine.odm.mongodb.document_manager
            classes:
                timeline:  Yo\UrBundle\Document\Timeline
                action:    Yo\UrBundle\Document\Action
                component: Yo\UrBundle\Document\Component
                action_component: Yo\UrBundle\Document\ActionComponent
```

Then, create documents: on `Yo\UrlBundle`

# 2) Create Timeline document

```php
<?php

namespace Yo\UrBundle\Document;

use Spy\TimelineBundle\Document\Timeline as BaseTimeline;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class Timeline extends BaseTimeline
{
    /**
     * @ODM\Id
     */
    protected $id;

    /**
     * @ODM\ReferenceOne(targetDocument="Action", cascade={"all"})
     */
    protected $action;

    /**
     * @ODM\ReferenceOne(targetDocument="Component", cascade={"all"})
     */
    protected $subject;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }
}
```

# 3) Create Action document

```php
<?php

namespace Yo\UrBundle\Document;

use Spy\TimelineBundle\Document\Action as BaseAction;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class Action extends BaseAction
{
    /**
     * @ODM\Id
     */
    protected $id;

    /**
     * @ODM\ReferenceMany(targetDocument="ActionComponent", cascade={"all"})
     */
    protected $actionComponents;

    /**
     * @ODM\ReferenceOne(targetDocument="Component")
     */
    protected $subject;
}
```

# 4) Create Component document

```php
<?php

namespace Yo\UrBundle\Document;

use Spy\TimelineBundle\Document\Component as BaseComponent;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class Component extends BaseComponent
{
    /**
     * @ODM\Id
     */
    protected $id;
}
```

# 5) Create ActionComponent document

```php
<?php

namespace Yo\UrBundle\Document;

use Spy\TimelineBundle\Document\ActionComponent as BaseActionComponent;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class ActionComponent extends BaseActionComponent
{
    /**
     * @ODM\Id
     */
    protected $id;

    /**
     * @ODM\ReferenceOne(targetDocument="Action")
     */
    protected $action;

    /**
     * @ODM\ReferenceOne(targetDocument="Component")
     */
    protected $component;
}
```

That's all

[index](https://github.com/stephpy/timeline-bundle/blob/master/Resources/doc/index.markdown)
