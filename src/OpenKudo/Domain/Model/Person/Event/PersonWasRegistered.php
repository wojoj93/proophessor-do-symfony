<?php

namespace OpenKudo\Domain\Model\Person\Event;

use OpenKudo\Domain\Model\Person\Email;
use OpenKudo\Domain\Model\Person\PersonId;
use Prooph\EventSourcing\AggregateChanged;

final class PersonWasRegistered extends AggregateChanged
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

    public static function registered(PersonId $personId, string $firstName, string $lastName, string $nickName, Email $email)
    {
        $event = self::occur(
            $personId->toString(),
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'nick_name' => $nickName,
                'email' => (string) $email,

            ]
        );
        $event->personId = $personId;
        $event->firstName = $firstName;
        $event->lastName = $lastName;
        $event->nickName = $nickName;
        $event->email = $email;

        return $event;
    }

    public function personId() : PersonId
    {
        if ($this->personId === null) {
            $this->personId = PersonId::fromString($this->aggregateId());
        }
        
        return $this->personId;
    }
    
    public function firstName() : string
    {
        if ($this->firstName === null) {
            $this->firstName = (string) $this->payload['first_name'];
        }

        return $this->firstName;
    }

    public function lastName() : string
    {
        if ($this->lastName === null) {
            $this->lastName = (string) $this->payload['last_name'];
        }

        return $this->lastName;
    }

    public function nickName() : string
    {
        if ($this->nickName === null) {
            $this->nickName = (string) $this->payload['nick_name'];
        }

        return $this->nickName;
    }

    public function email() : Email
    {
        if ($this->email === null) {
            $this->email = Email::fromString($this->payload['email']);
        }

        return $this->email;
    }
}
