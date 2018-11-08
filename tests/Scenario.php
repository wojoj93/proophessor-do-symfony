<?php

namespace Tests;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\DomainEvent;
use Prooph\Common\Messaging\Message;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\StreamName;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Exception\MessageDispatchException;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;

class Scenario
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var CommandRouter
     */
    private $commandRouter;

    /**
     * @var ActionEventEmitterEventStore
     */
    private $eventStore;

    /**
     * @var MessageCollector
     */
    private $messageCollector;

    /**
     * @var TestCase
     */
    private $testCase;

    /**
     * Scenario constructor.
     *
     * @param CommandBus                            $commandBus
     * @param CommandRouter                         $commandRouter
     * @param ActionEventEmitterEventStore|EventBus $eventBus
     * @param TestCase                              $testCase
     */
    public function __construct(
        CommandBus $commandBus,
        CommandRouter $commandRouter,
        ActionEventEmitterEventStore $eventBus,
        TestCase $testCase
    ) {
        $this->commandBus = $commandBus;
        $this->commandRouter = $commandRouter;
        $this->eventStore = $eventBus;
        $this->testCase = $testCase;
        $this->messageCollector = new MessageCollector();
        $this->eventStore->attach(ActionEventEmitterEventStore::EVENT_APPEND_TO, $this->messageCollector, 1);
        $this->commandBus->attach(MessageBus::EVENT_DISPATCH, $this->messageCollector, 1);
    }

    public function given(...$events): self
    {
        $this->commandRouter->detachFromMessageBus($this->commandBus);

        foreach ($events as $event) {
            try {
                $this->dispatch($event);
            } catch (MessageDispatchException $e) {
                continue;
            }
        }

        $this->commandRouter->attachToMessageBus($this->commandBus);

        return $this;
    }

    public function when(Message $message): self
    {
        $this->dispatch($message);

        return $this;
    }

    public function then(...$expectedMessages)
    {
        $collectedMessages = $this->messageCollector->all();
        foreach ($expectedMessages as $expectedMessage) {
            $this->assertThatMessageWasCollected($expectedMessage, $collectedMessages);
        }

        return $this;
    }

    private function dispatch(Message $message): void
    {
        if ($message instanceof Command) {
            $this->commandBus->dispatch($message);
        }
        if ($message instanceof DomainEvent) {
            $this->eventStore->dispatch($message);
        }
    }

    private function findMessage(Message $expectedMessage, array $collectedMessages): ?Message
    {
        foreach ($collectedMessages as $collectedMessage) {
            if (false === $collectedMessage instanceof $expectedMessage) {
                continue;
            }
            if (false === $collectedMessage->uuid()->equals($expectedMessage->uuid())) {
                continue;
            }

            return $collectedMessage;
        }

        if ($expectedMessage instanceof AggregateChanged) {
            return $this->findAggregateChangedEvent($expectedMessage);
        }

        return null;
    }

    private function findAggregateChangedEvent(AggregateChanged $aggregateChanged)
    {
        $streams = $this->eventStore->fetchStreamNames(null, null);

        foreach ($streams as $stream) {
            /**
             * @var $stream StreamName
             */
            foreach($this->eventStore->load($stream) as $event) {
                if (false === $event instanceof $aggregateChanged) {
                    continue;
                }

                if ($event->aggregateId() !== $aggregateChanged->aggregateId()) {
                    continue;
                }

                return $event;
            }
        }

        return null;
    }

    private function assertThatMessageWasCollected(Message $expectedMessage, $collectedMessages)
    {
        $collectedMessage = $this->findMessage($expectedMessage, $collectedMessages);

        if (null === $collectedMessage) {
            throw new AssertionFailedError(
                sprintf(
                    'Expected that message with class "%s" and aggregateId "%s", to be collected.',
                    get_class($expectedMessage),
                    $expectedMessage->uuid()
                )
            );
        }

        $this->testCase->addToAssertionCount(1);
    }
}
