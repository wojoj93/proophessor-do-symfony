<?php

namespace OpenKudo\Domain\Model\Person\Handler;

use OpenKudo\Domain\Model\Person\Command\RegisterPerson;
use OpenKudo\Domain\Model\Person\Person;
use OpenKudo\Domain\Model\Person\PersonList;

final class RegisterPersonHandler
{
    /**
     * @var PersonList
     */
    private $personList;

    /**
     * PersonRegisteredHandler constructor.
     *
     * @param PersonList $personList
     */
    public function __construct(PersonList $personList)
    {
        $this->personList = $personList;
    }

    public function __invoke(RegisterPerson $command)
    {
        $person = Person::register(
            $command->personId(),
            $command->firstName(),
            $command->lastName(),
            $command->nickName(),
            $command->email()
        );

        $this->personList->save($person);
    }
}
