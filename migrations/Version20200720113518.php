<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200720113518 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add the relation ManyToOne between Product and Bottle';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD bottle_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADDCF9352B FOREIGN KEY (bottle_id) REFERENCES bottle (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADDCF9352B ON product (bottle_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADDCF9352B');
        $this->addSql('DROP INDEX IDX_D34A04ADDCF9352B ON product');
        $this->addSql('ALTER TABLE product DROP bottle_id');
    }
}
