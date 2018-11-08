<?php

namespace OpenKudo\Domain\Infrastructure\Repository;

use OpenKudo\Domain\Model\Person\Person;
use OpenKudo\Domain\Model\Person\PersonId;
use OpenKudo\Domain\Model\Person\PersonList;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

class EventStorePersonList extends AggregateRepository implements PersonList
{
    public function save(Person $person)
    {
        $this->saveAggregateRoot($person);
    }

    public function get(PersonId $id)
    {
        return $this->getAggregateRoot($id->toString());
    }
}
