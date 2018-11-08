<?php

namespace Tests\OpenKudo\Domain\Model\Person\Command;

use OpenKudo\Domain\Model\Person\Command\RegisterPerson;
use OpenKudo\Domain\Model\Person\Email;
use OpenKudo\Domain\Model\Person\Event\PersonWasRegistered;
use OpenKudo\Domain\Model\Person\PersonId;
use Tests\ScenarioTestCase;

class RegisterPersonTest extends ScenarioTestCase
{
    /**
     * @test
     */
    public function it_registers_person(): void
    {
        $this->scenario()->when(
                RegisterPerson::register(
                    $personId = PersonId::generate()->toString(),
                    'FirstName',
                    'LastName',
                    'NickName',
                    'email@email.com'
                )
            )->then(
                PersonWasRegistered::registered(
                    PersonId::fromString($personId),
                    'FirstName',
                    'LastName',
                    ' NickName',
                    Email::fromString('email@email.com')
                )
            );
    }
}
