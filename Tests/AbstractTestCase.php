<?php

namespace Highco\TimelineBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\Tools\SchemaTool;

abstract class AbstractTestCase extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient(
            array('environment' => 'test')
        );

        $this->container = $this->client->getContainer();
        $this->doctrine  = $this->container->get('doctrine');
        $this->em        = $this->doctrine->getEntityManager();

        $this->generateSchema();

        $this->redis = $this->container->get('snc_redis.test_client');

        $this->container->get('highco.timeline.provider.redis')
            ->setRedis($this->redis);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->dropSchema();
        $this->redis->flushDb();
    }


    /**
     * generateSchema
     *
     * @access protected
     * @return void
     */
    protected function generateSchema()
    {
        // Get the metadatas of the application to create the schema.
        $metadatas = $this->getMetadatas();

        if ( ! empty($metadatas))
        {
            $tool = new SchemaTool($this->em);
            $tool->createSchema($metadatas);
        }
        else
        {
            throw new Doctrine\DBAL\Schema\SchemaException('No Metadata Classes to process.');
        }
    }

    /**
     * dropSchema
     *
     * @access protected
     * @return void
     */
    protected function dropSchema()
    {
        // Get the metadatas of the application to create the schema.
        $metadatas = $this->getMetadatas();

        if ( ! empty($metadatas))
        {
            $tool = new SchemaTool($this->em);
            $tool->dropSchema($metadatas);
        }
        else
        {
            throw new Doctrine\DBAL\Schema\SchemaException('No Metadata Classes to process.');
        }
    }

    /**
     * Overwrite this method to get specific metadatas.
     *
     * @return Array
     */
    protected function getMetadatas()
    {
        return $this->em->getMetadataFactory()->getAllMetadata();
    }
}
