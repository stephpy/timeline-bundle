# Providers

## Adding a provider

Create the class:

    use Highco\TimelineBundle\Timeline\Provider\InterfaceProvider;

    MyProvider implements InterfaceProvider
    {
        public function getWall($params, $options = array())
        {
            // ...
        }

        public function getTimeline($params, $options = array())
        {
            // ...
        }


        public function add(TimelineAction $timelineAction, $context, $subjectModel, $subjectId)
        {
            // ...
        }

        public function setEntityRetriever(InterfaceEntityRetriever $entityRetriever = null)
        {
            // ....
        }

    }

Define this as a service, and replace on you config.yml:

    highco_timeline:
        provider: *your_service*

        # Entity retriever is the instance which will retrieve entity/docs from storage, typically doctrine.dbal/propel/doctrine.odm ...
        # Because redis return only ids and provider should return a collection of TimelineAction models.

        entity_retriever: *your_service*


## Provider "REDIS"

Depend on SncRedis, it actually uses predis

**Redis > 1.1 is recquired on server**

