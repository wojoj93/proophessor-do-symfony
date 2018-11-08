<?php

namespace OpenKudo\Domain\Projection\Kudo;

use Doctrine\DBAL\Connection;
use OpenKudo\Domain\Model\Kudo\Event\ThankYouWasPosted;
use OpenKudo\Domain\Projection\Table;
use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\EventStore\Projection\ReadModelProjector;

final class ThankYouProjection implements ReadModelProjection
{
    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream('event_stream')
            ->init(function () {
                return ['count' => 0];
            })
            ->when([
                ThankYouWasPosted::class => function ($state, ThankYouWasPosted $event) {
                    /**
                     * @var $readModel ThankYouReadModel
                     */
                    $readModel = $this->readModel();

                    $receiverIds = array_map(function ($receiverId) {
                        return $receiverId->toString();
                    }, $event->receiversIds());

                    $readModel->stack('insert', [
                        'id' => $event->thankYouId()->toString(),
                        'giver_id' => $event->giverId()->toString(),
                        'reason' => $event->reason(),
                        'amount' => $event->amount(),
                        'receiver_ids' => $receiverIds,
                    ]);

                    $state['count']++;
                    return $state;
                }
            ]);

        return $projector;
    }
}
