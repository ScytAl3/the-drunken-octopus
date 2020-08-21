<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200821150134 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create order items table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, ordered_id INT DEFAULT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_62809DB0AA60395A (ordered_id), INDEX IDX_62809DB04584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0AA60395A FOREIGN KEY (ordered_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB04584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE orders ADD order_items_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE8A484C35 FOREIGN KEY (order_items_id) REFERENCES order_items (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE8A484C35 ON orders (order_items_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE8A484C35');
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP INDEX IDX_E52FFDEE8A484C35 ON orders');
        $this->addSql('ALTER TABLE orders DROP order_items_id');
    }
}
