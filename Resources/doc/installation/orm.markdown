ORM Driver
==========

You'll have to define entities on a bundle, you can add it to anyone bundle, in this example will use `Yo\UrBundle`.

# 1) Define configuration

```yml
#config.yml
spy_timeline:
    drivers:
        orm:
            object_manager: doctrine.orm.entity_manager
            classes:
                query_builder: ~ # Spy\TimelineBundle\Driver\ORM\QueryBuilder\QueryBuilder
                timeline:  Yo\UrBundle\Entity\Timeline
                action:    Yo\UrBundle\Entity\Action
                component: Yo\UrBundle\Entity\Component
                action_component: Yo\UrBundle\Entity\ActionComponent
```

Then, create entities: on `Yo\UrlBundle`

# 2) Create Timeline entity

```php
<?php

namespace Yo\UrBundle\Entity;

use Spy\TimelineBundle\Entity\Timeline as BaseTimeline;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="spy_timeline")
 */
class Timeline extends BaseTimeline
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Yo\UrlBundle\Entity\Action", inversedBy="timelines")
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     */
    protected $action;

    /**
     * @ORM\ManyToOne(targetEntity="Yo\UrlBundle\Entity\Component")
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $subject;
}
```

# 3) Create Action entity

```php
<?php

namespace Yo\UrBundle\Entity;

use Spy\TimelineBundle\Entity\Action as BaseAction;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="spy_timeline_action")
 */
class Action extends BaseAction
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="ActionComponent", mappedBy="action", cascade={"persist"})
     */
    protected $actionComponents;

    /**
     * @ORM\OneToMany(targetEntity="Timeline", mappedBy="action")
     */
    protected $timelines;
}
```

# 4) Create Component entity

```php
<?php

namespace Yo\UrBundle\Entity;

use Spy\TimelineBundle\Entity\Component as BaseComponent;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="spy_timeline_component")
 */
class Component extends BaseComponent
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
```

# 5) Create ActionComponent entity

```php
<?php

namespace Yo\UrBundle\Entity;

use Spy\TimelineBundle\Entity\ActionComponent as BaseActionComponent;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="spy_timeline_action_component")
 */
class ActionComponent extends BaseActionComponent
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Yo\UrBundle\Entity\Action", inversedBy="actionComponents")
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $action;

    /**
     * @ORM\ManyToOne(targetEntity="Yo\UrBundle\Entity\Component")
     * @ORM\JoinColumn(name="component_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $component;
}
```

That's all

[index](https://github.com/stephpy/timeline-bundle/blob/master/Resources/doc/index.markdown)
