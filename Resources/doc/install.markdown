# Installation

# Step 1, Download it

Add this to your composer.json

```
"stephpy/TimelineBundle": "dev-master"
```

Then

```
php composer.phar update # or install
```

# Step 2: Enable the bundle

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Spy\TimelineBundle\SpyTimelineBundle(),
    );
}
```

Choose your driver:

- [orm](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/installation/orm.markdown)
- [redis](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/installation/redis.markdown)

# Step 4: (If you use Doctrine db_driver )Define your TimelineAction class

```php
<?php
//Acme/YourBundle/Entity/TimelineAction
use Spy\TimelineBundle\Entity\TimelineAction as BaseTimelineAction;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="timeline_action")
 */
class TimelineAction extends BaseTimelineAction
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
```

Don't forget to define it on `config.yml`

```
spy_timeline:
	... other configuration ...
	timeline_action_class: Acme\YourBundle\Entity\TimelineAction
	... other configuration ...
```

# Step 5: (If you use Doctrine ORM Provider) Define your Timeline class

```php
<?php
//Acme/YourBundle/Entity/Timeline

use Spy\TimelineBundle\Entity\Timeline as BaseTimeline;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *  indexes={
 *      @ORM\index(name="primary_created_at", columns={"type", "context", "subject_model", "subject_id", "created_at"}),
 *  }
 * )
 */
class Timeline extends BaseTimeline
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="TimelineAction")
     * @ORM\JoinColumn(name="timeline_action_id", referencedColumnName="id")
     *
     * NOTE: This join-column will merge with type, context, subjectModel, and subjectId to form a composite primary
     * key
     *
     * @var TimelineAction
     */
    protected $timelineAction;

}
```

Don't forget to define it on `config.yml`

```
spy_timeline:
	... other configuration ...
    provider:
        service: spy_timeline.provider.doctrine.orm
        object_manager: doctrine.orm.entity_manager
        timeline_class: Acme\YourBundle\Entity\Timeline
	... other configuration ...
```

Then, look at full configuration on [index](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/index.markdown)
