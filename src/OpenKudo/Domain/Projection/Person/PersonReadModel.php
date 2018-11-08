<?php

namespace OpenKudo\Domain\Projection\Person;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use OpenKudo\Domain\Projection\Table;
use Prooph\EventStore\Projection\AbstractReadModel;

final class PersonReadModel extends AbstractReadModel
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
        $personTable = $schema->createTable(Table::PERSON);
        $personTable->addColumn('id', Type::STRING, ['length' => 36]);
        $personTable->addColumn('first_name', Type::STRING, ['length' => 255]);
        $personTable->addColumn('last_name', Type::STRING, ['length' => 255]);
        $personTable->addColumn('nick_name', Type::STRING, ['length' => 255]);
        $personTable->addColumn('email', Type::STRING, ['length' => 255]);
        $personTable->setPrimaryKey(['id']);
        $personTable->addUniqueIndex(['nick_name']);
        $personTable->addUniqueIndex(['email']);

        $platform = $this->connection->getDatabasePlatform();
        foreach ($schema->toSql($platform) as $query) {
            $this->connection->exec($query);
        }
    }

    public function isInitialized(): bool
    {
        return $this->connection->getSchemaManager()->tablesExist([Table::PERSON]);
    }

    public function reset(): void
    {
        $this->connection->delete(Table::PERSON, []);
    }

    public function delete(): void
    {
        $this->connection->getSchemaManager()->dropTable(Table::PERSON);
    }

    public function insert(array $data) : void
    {
        $this->connection->insert(Table::PERSON, $data);
    }
}
