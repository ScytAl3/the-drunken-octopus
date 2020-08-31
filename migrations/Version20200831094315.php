<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200831094315 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create shipping_addresses table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shipping_addresses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, address2 VARCHAR(255) DEFAULT NULL, country VARCHAR(255) NOT NULL, zipcode VARCHAR(12) NOT NULL, city VARCHAR(255) NOT NULL, phone VARCHAR(16) DEFAULT NULL, same_for_billing TINYINT(1) DEFAULT \'1\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE shipping_addresses');
    }
}
