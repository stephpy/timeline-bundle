# Providers

## Adding a provider

Create the class:


````php
<?php
    use Highco\TimelineBundle\Timeline\Provider\ProviderInterface;

    MyProvider implements ProviderInterface
    {
        public function getWall($params, $options = array())
        {
            // ...
        }

        public function persist(TimelineAction $timelineAction, $context, $subjectModel, $subjectId)
        {
            // ...
        }

		public function flush()
		{
			// ...
		}

    }
````

Define this as a service, and replace on you config.yml:

````yaml
    highco_timeline:
        provider: *your_service*
````

## Provider "REDIS"

Depend on SncRedis, it actually uses predis

**Redis > 1.1 is recquired on server**

