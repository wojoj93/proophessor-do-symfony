<?php

namespace OpenKudo\Domain\Model\Kudo\Command;

use OpenKudo\Domain\Model\Kudo\ThankYouId;
use OpenKudo\Domain\Model\Person\PersonId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

final class PostThankYou extends Command implements PayloadConstructable
{
    use PayloadTrait;

    /**
     * @param string $giverId
     * @param array  $receiversIds
     * @param string $reason
     * @param int    $amount
     * @param string $thankYouId
     *
     * @return PostThankYou
     */
    public static function byGiver(string $giverId, array $receiversIds, string $reason, int $amount, string $thankYouId)
    {
        return new self(
            [
                'thank_you_id' => $thankYouId,
                'giver_id' => $giverId,
                'reason' => $reason,
                'amount' => $amount,
                'receivers_ids' => $receiversIds,
            ]
        );
    }

    /**
     * @return ThankYouId
     */
    public function thankYouId() : ThankYouId
    {
        return ThankYouId::fromString($this->payload['thank_you_id']);
    }

    /**
     * @return PersonId
     */
    public function giverId() : PersonId
    {
        return PersonId::fromString($this->payload['giver_id']);
    }

    /**
     * @return string
     */
    public function reason() : string
    {
        return $this->payload['reason'];
    }

    /**
     * @return int
     */
    public function amount() : int
    {
        return $this->payload['amount'];
    }

    /**
     * @return PersonId[]
     */
    public function receiversId() : array
    {
        return array_map(
            function (string $id) {
                return PersonId::fromString($id);
            },
            $this->payload['receivers_ids']
        );
    }
}
