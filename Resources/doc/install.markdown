# Installation

# Step 1, Download it

## Via deps

Add this to your deps file

    [TimelineBundle]
        git=git://github.com/stephpy/TimelineBundle
        target=/bundles/Highco/TimelineBundle

Then

    php bin/vendors install

## Via composer

Add this to your composer.json

        "stephpy/TimelineBundle": "dev-master"

Then

    php composer.phar update # or install

## Via submodule

    git submodule add git://github.com/stephpy/TimelineBundle vendor/bundles/Highco/TimelineBundle
    git submodule update --init


# Step 2: Configure autoload

````php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Highco' => __DIR__.'/../vendor/bundles',
));
````

# Step 3: Enable the bundle

````php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Highco\TimelineBundle\HighcoTimelineBundle(),
    );
}
````

Then, look at full configuration on [index](https://github.com/stephpy/TimelineBundle/blob/master/Resources/doc/index.markdown)
