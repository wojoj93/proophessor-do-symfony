<?php

namespace OpenKudo\Domain\Model\Person\Command;

use OpenKudo\Domain\Model\Person\Email;
use OpenKudo\Domain\Model\Person\PersonId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

final class RegisterPerson extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function register(string $id, string $firstName, string $lastName, string $nickName, string $email)
    {
        return new self(
            [
                'person_id' => $id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'nick_name' => $nickName,
                'email' => $email
            ]
        );
    }
    
    public function personId() : PersonId
    {
        return PersonId::fromString($this->payload['person_id']);
    }
    
    public function firstName() : string
    {
        return $this->payload['first_name'];
    }
    
    public function lastName() : string
    {
        return $this->payload['last_name'];
    }
    
    public function nickName() : string
    {
        return $this->payload['nick_name'];
    }

    public function email() : Email
    {
        return Email::fromString($this->payload['email']);
    }
}
