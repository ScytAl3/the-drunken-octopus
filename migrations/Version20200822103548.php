<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200822103548 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create purchase_products table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchase_products (id INT AUTO_INCREMENT NOT NULL, purchase_order_id INT DEFAULT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_9A784908A45D7E6A (purchase_order_id), INDEX IDX_9A7849084584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_products ADD CONSTRAINT FK_9A784908A45D7E6A FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders (id)');
        $this->addSql('ALTER TABLE purchase_products ADD CONSTRAINT FK_9A7849084584665A FOREIGN KEY (product_id) REFERENCES products (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE purchase_products');
    }
}
