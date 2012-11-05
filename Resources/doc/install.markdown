# Installation

# Step 1, Download it

## Via deps

Add this to your deps file

```
[TimelineBundle]
    git=git://github.com/stephpy/TimelineBundle
    target=/bundles/Highco/TimelineBundle
```

Then

```
php bin/vendors install
```

## Via composer

Add this to your composer.json

```
"stephpy/TimelineBundle": "dev-master"
```

Then

```
php composer.phar update # or install
```

## Via submodule

```
git submodule add git://github.com/stephpy/TimelineBundle vendor/bundles/Highco/TimelineBundle
git submodule update --init
```


# Step 2: Configure autoload

```php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Highco' => __DIR__.'/../vendor/bundles',
));
```

# Step 3: Enable the bundle

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Highco\TimelineBundle\HighcoTimelineBundle(),
    );
}
```

# Step 4: (If you use Doctrine db_driver )Define your TimelineAction class

```php
<?php
//Acme/YourBundle/Entity/TimelineAction
use Highco\TimelineBundle\Entity\TimelineAction as BaseTimelineAction;
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
highco_timeline:
	... other configuration ...
	timeline_action_class: Acme\YourBundle\Entity\TimelineAction
	... other configuration ...
```

# Step 5: (If you use Doctrine ORM Provider) Define your Timeline class

```php
<?php
//Acme/YourBundle/Entity/Timeline

use Highco\TimelineBundle\Entity\Timeline as BaseTimeline;
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
highco_timeline:
	... other configuration ...
    provider:
        service: highco.timeline.provider.doctrine.orm
        object_manager: doctrine.orm.entity_manager
        timeline_class: Acme\YourBundle\Entity\Timeline
	... other configuration ...
```

Then, look at full configuration on [index](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/index.markdown)
