<?php

namespace OpenKudo\Domain\Model\Kudo\Event;

use OpenKudo\Domain\Model\Kudo\ThankYouId;
use OpenKudo\Domain\Model\Person\PersonId;
use Prooph\EventSourcing\AggregateChanged;

final class ThankYouWasPosted extends AggregateChanged
{
    /**
     * @var ThankYouId
     */
    private $thankYouId;

    /**
     * @var PersonId
     */
    private $giverId;

    /**
     * @var PersonId[]
     */
    private $receiversIds;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var int
     */
    private $amount;

    /**
     * @param PersonId $giverId
     * @param PersonId[] $receiversIds
     * @param string $reason
     * @param integer $amount
     * @param ThankYouId $thankYouId
     *
     * @return self
     */
    public static function byGiver(PersonId $giverId, array $receiversIds, string $reason, int $amount, ThankYouId $thankYouId)
    {
        $event = self::occur(
            $thankYouId->toString(),
            [
                'giver_id' => $giverId->toString(),
                'reason' => $reason,
                'amount' => $amount,
                'receivers_ids' => array_map(function (PersonId $personId) {
                    return $personId->toString();
                }, $receiversIds)
            ]
        );
        $event->thankYouId = $thankYouId;
        $event->giverId = $giverId;
        $event->reason = $reason;
        $event->amount = $amount;
        $event->receiversIds = $receiversIds;

        return $event;
    }

    /**
     * @return ThankYouId
     */
    public function thankYouId()
    {
        if ($this->thankYouId === null) {
            $this->thankYouId = ThankYouId::fromString($this->aggregateId());
        }
        return $this->thankYouId;
    }

    /**
     * @return PersonId
     */
    public function giverId()
    {
        if ($this->giverId === null) {
            $this->giverId = PersonId::fromString($this->payload['giver_id']);
        }
        return $this->giverId;
    }

    /**
     * @return PersonId[]
     */
    public function receiversIds()
    {
        if ($this->receiversIds === null) {
            $this->receiversIds = [];
            foreach ($this->payload['receivers_ids'] as $receiverId) {
                $this->receiversIds[] = PersonId::fromString($receiverId);
            }
        }

        return $this->receiversIds;
    }

    /**
     * @return string
     */
    public function reason()
    {
        if ($this->reason === null) {
            $this->reason = $this->payload['reason'];
        }

        return $this->reason;
    }

    /**
     * @return int
     */
    public function amount()
    {
        if ($this->amount === null) {
            $this->amount = $this->payload['amount'];
        }

        return $this->amount;
    }
}
