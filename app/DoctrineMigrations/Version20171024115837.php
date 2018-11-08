<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171024115837 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql');

        foreach (explode(';', file_get_contents(__DIR__.'/../../vendor/prooph/pdo-event-store/scripts/postgres/01_event_streams_table.sql')) as $sql) {
            if (strlen(trim($sql)) === 0){
                continue;
            }

            $this->addSql($sql.';');
        }
        foreach (explode(';', file_get_contents(__DIR__.'/../../vendor/prooph/pdo-event-store/scripts/postgres/02_projections_table.sql')) as $sql) {
            if (strlen(trim($sql)) === 0){
                continue;
            }

            $this->addSql($sql.';');
        }
    }

    public function down(Schema $schema)
    {
    }
}
