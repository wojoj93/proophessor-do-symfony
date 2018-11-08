<?php

namespace Tests;

use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;

abstract class ScenarioTestCase extends ContainerTestCase
{
    /**
     * @var Scenario
     */
    private $scenario;

    private $container;

    protected function setUp()
    {
        parent::setUp();

        $this->container = static::getContainer();

        $this->scenario = new Scenario(
            $this->container->get('prooph_service_bus.kudo_command_bus'),
            $this->container->get('prooph_service_bus.kudo_command_bus.router'),
            $this->container->get('prooph_event_store.kudo_store'),
            $this
        );

        $eventStore = $this->container->get('prooph_event_store.kudo_store');

        $eventStore->create(new Stream(new StreamName('event_stream'), new \ArrayIterator([])));
    }

    protected function tearDown()
    {
        $this->scenario = null;
        parent::tearDown();
    }

    public function scenario() : Scenario
    {
        return $this->scenario;
    }
}
