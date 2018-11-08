<?php

namespace OpenKudo\Domain\Model\Person;

use Assert\Assertion;
use OpenKudo\Domain\Model\Person\Exception\InvalidEmailException;

class Email
{
    private $email;

    /**
     * Email constructor.
     *
     * @param $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;

        $this->assertEmail($email);
    }

    public static function fromString(string $email)
    {
        $self = new self($email);

        return $self;
    }

    private function assertEmail($email)
    {
        try {
            Assertion::email($email);
        } catch (Exception $e) {
            throw new InvalidEmailException($e->getMessage());
        }
    }

    public function __toString()
    {
        return $this->email;
    }
}
