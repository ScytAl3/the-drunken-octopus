<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200904132154 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add shipping and billing addresses fields to purchase_order table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_orders ADD shipping_address_id INT DEFAULT NULL, ADD billing_address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_orders ADD CONSTRAINT FK_3E40FFBB4D4CFF2B FOREIGN KEY (shipping_address_id) REFERENCES shipping_addresses (id)');
        $this->addSql('ALTER TABLE purchase_orders ADD CONSTRAINT FK_3E40FFBB79D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES shipping_addresses (id)');
        $this->addSql('CREATE INDEX IDX_3E40FFBB4D4CFF2B ON purchase_orders (shipping_address_id)');
        $this->addSql('CREATE INDEX IDX_3E40FFBB79D0C0E4 ON purchase_orders (billing_address_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_orders DROP FOREIGN KEY FK_3E40FFBB4D4CFF2B');
        $this->addSql('ALTER TABLE purchase_orders DROP FOREIGN KEY FK_3E40FFBB79D0C0E4');
        $this->addSql('DROP INDEX IDX_3E40FFBB4D4CFF2B ON purchase_orders');
        $this->addSql('DROP INDEX IDX_3E40FFBB79D0C0E4 ON purchase_orders');
        $this->addSql('ALTER TABLE purchase_orders DROP shipping_address_id, DROP billing_address_id');
    }
}
