<?php
declare(strict_types=1);

namespace OpenKudo\Domain\Projection\Kudo;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use OpenKudo\Domain\Projection\Table;
use Prooph\EventStore\Projection\AbstractReadModel;

final class ThankYouReadModel extends AbstractReadModel
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function init(): void
    {
        $schema = new Schema();
        $thankYou = $schema->createTable(Table::THANK_YOU);
        $thankYou->addColumn('id', Type::STRING, ['length' => 36]);
        $thankYou->addColumn('giver_id', Type::STRING, ['length' => 36]);
        $thankYou->addColumn('reason', Type::TEXT);
        $thankYou->addColumn('amount', Type::INTEGER, ['unsigned' => true]);
        $thankYou->setPrimaryKey(['id']);

        $thankYouReceiver = $schema->createTable(Table::THANK_YOU_RECEIVER);
        $thankYouReceiver->addColumn('thank_you_id', Type::STRING, ['length' => 36]);
        $thankYouReceiver->addColumn('receiver_id', Type::STRING, ['length' => 36]);
        $thankYouReceiver->addUniqueIndex(['thank_you_id', 'receiver_id']);
        $thankYouReceiver->addForeignKeyConstraint(Table::THANK_YOU, ['thank_you_id'], ['id'], ['onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE']);

        $platform = $this->connection->getDatabasePlatform();
        foreach ($schema->toSql($platform) as $query) {
            $this->connection->exec($query);
        }
    }

    public function isInitialized(): bool
    {
        return $this->connection->getSchemaManager()->tablesExist([Table::THANK_YOU_RECEIVER, Table::THANK_YOU]);
    }

    public function reset(): void
    {
        $this->connection->delete(Table::THANK_YOU_RECEIVER, []);
        $this->connection->delete(Table::THANK_YOU, []);
    }

    public function delete(): void
    {
        $this->connection->getSchemaManager()->dropTable(Table::THANK_YOU_RECEIVER);
        $this->connection->getSchemaManager()->dropTable(Table::THANK_YOU);
    }

    public function insert(array $data) : void
    {
        $receiverIds =  $data['receiver_ids'];
        unset($data['receiver_ids']);

        $this->connection->insert(Table::THANK_YOU, $data);

        foreach ($receiverIds as $receiverId) {
            $this->connection->insert(Table::THANK_YOU_RECEIVER, ['thank_you_id' => $data['id'], 'receiver_id' => $receiverId]);
        }
    }
}
