<?php

namespace OpenKudo\Domain\Infrastructure\Repository;

use OpenKudo\Domain\Model\Kudo\ThankYou;
use OpenKudo\Domain\Model\Kudo\ThankYouId;
use OpenKudo\Domain\Model\Kudo\ThankYouList;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

final class EventStoreThankYouList extends AggregateRepository implements ThankYouList
{
    public function save(ThankYou $thankYou)
    {
        $this->saveAggregateRoot($thankYou);
    }

    public function get(ThankYouId $id)
    {
        return $this->getAggregateRoot($id->toString());
    }
}
