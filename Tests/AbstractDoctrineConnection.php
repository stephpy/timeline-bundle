<?php

namespace Highco\TimelineBundle\Tests;

require_once __DIR__.'/../../../../../app/AppKernel.php';

use Doctrine\ORM\Tools\SchemaTool;

class AbstractDoctrineConnection extends \PHPUnit_Framework_TestCase
{
	protected $kernel;

	/**
     * setUp
     *
     * @access public
     * @return void
     */
    public function setUp()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();

		// this step is for users which not update their config_test.yml :/
		// @todo, let user choice to use his config_test.yml connection
        $conn = $this->container->get('doctrine.dbal.connection_factory')->createConnection(array(
            'dbname' => 'highco_timeline_test',
            'host' => 'localhost',
            'port' => NULL,
            'user' => 'root',
            'password' => 'root',
            'driver' => 'pdo_sqlite',
            'charset' => 'UTF8',
            'memory' => true,
            'driverOptions' => array()
        ), null, new \Doctrine\Common\EventManager(), array());

        $this->container->set('doctrine.dbal.default_connection', $conn);

        $this->em        = $this->container->get('doctrine')->getEntityManager();

        $this->generateSchema();

        parent::setUp();
    }

    /**
     * tearDown
     *
     * @access public
     * @return void
     */
    public function tearDown()
    {
        // Shutdown the kernel.
        $this->kernel->shutdown();

		$this->dropSchema();

        parent::tearDown();
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
