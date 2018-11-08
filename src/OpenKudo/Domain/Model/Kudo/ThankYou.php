<?php

namespace OpenKudo\Domain\Model\Kudo;

use Assert\Assertion;
use OpenKudo\Domain\Model\Kudo\Event\ThankYouWasPosted;
use OpenKudo\Domain\Model\Kudo\Exception\InvalidAmountException;
use OpenKudo\Domain\Model\Kudo\Exception\InvalidReasonException;
use OpenKudo\Domain\Model\Kudo\Exception\InvalidReceiverIdException;
use OpenKudo\Domain\Model\Person\PersonId;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

final class ThankYou extends AggregateRoot
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
    private $receiversIds = [];

    /**
     * @var string
     */
    private $reason;

    /**
     * @var integer
     */
    private $amount;

    /**
     * @param PersonId $giverId
     * @param array    $receiversIds
     * @param string   $reason
     * @param int      $amount
     *
     * @return ThankYou
     */
    public static function post(PersonId $giverId, array $receiversIds = [], string $reason, int $amount, ThankYouId $thankYouId) : self
    {
        $self = new self();
        $self->assertReceiversId($receiversIds);
        $self->assertReason($reason);
        $self->assertAmount($amount);

        $self->recordThat(ThankYouWasPosted::byGiver($giverId, $receiversIds, $reason, $amount, $thankYouId));

        return $self;
    }

    /**
     * @return string representation of the unique identifier of the aggregate root
     */
    protected function aggregateId() : string
    {
        return $this->thankYouId->toString();
    }

    protected function whenThankYouWasPosted(ThankYouWasPosted $event)
    {
        $this->thankYouId = $event->thankYouId();
        $this->giverId = $event->giverId();
        $this->receiversIds = $event->receiversIds();
        $this->reason = $event->reason();
        $this->amount = $event->amount();
    }

    private function assertReceiversId(array $receiversIds)
    {
        try {
            Assertion::greaterOrEqualThan(count($receiversIds), 1);
        } catch (\Exception $e) {
            throw new InvalidReceiverIdException("Expected an array of at least one ReceiverId");
        }


        foreach ($receiversIds as $key => $receiverId) {
            try {
                Assertion::isInstanceOf($receiverId, PersonId::class);
            } catch (\Exception $e) {
                throw new InvalidReceiverIdException(sprintf('Expected %s instance, received %s at key %d', PersonId::class, gettype($receiverId), $key));
            }
        }
    }

    private function assertReason($reason)
    {
        try {
            Assertion::string($reason);
            Assertion::minLength($reason, 3);
        } catch (\Exception $e) {
            throw new InvalidReasonException($e->getMessage());
        }
    }

    private function assertAmount(int $amount)
    {
        try {
            Assertion::greaterOrEqualThan($amount, 1);
        } catch (\Exception $e) {
            throw new InvalidAmountException('The minimum amount to give is 1');
        }
    }

    /**
     * Apply given event
     */
    protected function apply(AggregateChanged $e): void
    {
        $handler = $this->determineEventHandlerMethodFor($e);
        if (! method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event handler method %s for aggregate root %s',
                $handler,
                get_class($this)
            ));
        }
        $this->{$handler}($e);
    }
    protected function determineEventHandlerMethodFor(AggregateChanged $e): string
    {
        return 'when' . implode(array_slice(explode('\\', get_class($e)), -1));
    }
}
