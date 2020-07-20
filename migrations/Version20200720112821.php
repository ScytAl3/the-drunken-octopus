<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200720112821 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add the relation ManyToOne between Product and Brewery';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD brewery_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADD15C960 FOREIGN KEY (brewery_id) REFERENCES brewery (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADD15C960 ON product (brewery_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADD15C960');
        $this->addSql('DROP INDEX IDX_D34A04ADD15C960 ON product');
        $this->addSql('ALTER TABLE product DROP brewery_id');
    }
}
