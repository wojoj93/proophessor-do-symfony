<?php

namespace OpenKudo\Domain\Model\Person;

interface PersonList
{
    public function save(Person $person);

    public function get(PersonId $id);
}
