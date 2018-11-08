<?php

namespace OpenKudo\Domain\Model\Person;

use Assert\Assertion;
use OpenKudo\Domain\Model\Person\Event\PersonWasRegistered;
use OpenKudo\Domain\Model\Person\Exception\InvalidFirstNameException;
use OpenKudo\Domain\Model\Person\Exception\InvalidLastNameException;
use OpenKudo\Domain\Model\Person\Exception\InvalidNickNameException;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

final class Person extends AggregateRoot
{
    /**
     * @var PersonId
     */
    private $personId;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $nickName;

    /**
     * @var Email
     */
    private $email;

    public static function register(PersonId $personId, string $firstName, string $lastName, string $nickName, Email $email) : Person
    {
        $self = new self;
        $self->assertFirstName($firstName);
        $self->assertLastName($lastName);
        $self->assertNickName($nickName);

        $self->recordThat(PersonWasRegistered::registered($personId, $firstName, $lastName, $nickName, $email));

        return $self;
    }

    protected function aggregateId(): string
    {
        return $this->personId->toString();
    }

    protected function whenPersonWasRegistered(PersonWasRegistered $event)
    {
        $this->personId = $event->personId();
        $this->firstName = $event->firstName();
        $this->lastName = $event->lastName();
        $this->nickName = $event->nickName();
        $this->email = $event->email();
    }

    private function assertFirstName($firstName)
    {
        try {
            Assertion::string($firstName);
            Assertion::minLength($firstName, 1);
        } catch (\Exception $e) {
            throw new InvalidFirstNameException($e->getMessage());
        }
    }

    private function assertLastName($lastName)
    {
        try {
            Assertion::string($lastName);
            Assertion::minLength($lastName, 1);
        } catch (\Exception $e) {
            throw new InvalidLastNameException($e->getMessage());
        }
    }

    private function assertNickName($nickName)
    {
        try {
            Assertion::string($nickName);
            Assertion::minLength($nickName, 3);
        } catch (\Exception $e) {
            throw new InvalidNickNameException($e->getMessage());
        }
    }

    /**
     * Apply given event
     */
    protected function apply(AggregateChanged $e): void
    {
        $handler = $this->determineEventHandlerMethodFor($e);
        if (! method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event handler method %s for aggregate root %s',
                $handler,
                get_class($this)
            ));
        }
        $this->{$handler}($e);
    }
    protected function determineEventHandlerMethodFor(AggregateChanged $e): string
    {
        return 'when' . implode(array_slice(explode('\\', get_class($e)), -1));
    }
}
