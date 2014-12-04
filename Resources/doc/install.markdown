# Installation

# Step 1, Download it

Add this to your composer.json

Please, prefer a tagged version from [here](https://packagist.org/packages/stephpy/timeline-bundle)

```
"stephpy/timeline-bundle": "dev-master"
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

# Step 3: Choose your driver

- [orm](https://github.com/stephpy/timeline-bundle/blob/master/Resources/doc/installation/orm.markdown)
- [odm](https://github.com/stephpy/timeline-bundle/blob/master/Resources/doc/installation/odm.markdown)
- [redis](https://github.com/stephpy/timeline-bundle/blob/master/Resources/doc/installation/redis.markdown)
