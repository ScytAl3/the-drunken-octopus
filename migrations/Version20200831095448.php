<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200831095448 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add relation between user and shipping_addresses tables';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipping_addresses ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shipping_addresses ADD CONSTRAINT FK_293F2421A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_293F2421A76ED395 ON shipping_addresses (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipping_addresses DROP FOREIGN KEY FK_293F2421A76ED395');
        $this->addSql('DROP INDEX IDX_293F2421A76ED395 ON shipping_addresses');
        $this->addSql('ALTER TABLE shipping_addresses DROP user_id');
    }
}
