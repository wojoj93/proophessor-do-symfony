<?php

namespace OpenKudo\Domain\Projection\Person;

use Doctrine\DBAL\Connection;
use OpenKudo\Domain\Model\Person\Event\PersonWasRegistered;
use OpenKudo\Domain\Projection\Table;
use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\EventStore\Projection\ReadModelProjector;

final class PersonProjection implements ReadModelProjection
{
    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream('event_stream')
                  ->init(function () {
                      return ['count' => 0];
                  })
                  ->when([
                      PersonWasRegistered::class => function ($state, PersonWasRegistered $event) {
                          /**
                           * @var $readModel PersonReadModel
                           */
                          $readModel = $this->readModel();

                          $readModel->stack('insert', [
                              'id' => $event->personId()->toString(),
                              'first_name' => $event->firstName(),
                              'last_name' => $event->lastName(),
                              'nick_name' => $event->nickName(),
                              'email' => $event->email(),
                          ]);

                          $state['count']++;
                          return $state;
                      }
                  ]);

        return $projector;
    }
}
