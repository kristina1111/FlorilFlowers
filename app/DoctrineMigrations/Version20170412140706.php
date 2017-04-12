<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170412140706 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_prices CHANGE currency_id currency_id INT DEFAULT NULL, CHANGE product_offer_id product_offer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_prices ADD CONSTRAINT FK_86B72CFD38248176 FOREIGN KEY (currency_id) REFERENCES currencies (id)');
        $this->addSql('ALTER TABLE product_prices ADD CONSTRAINT FK_86B72CFD98761E79 FOREIGN KEY (product_offer_id) REFERENCES product_offers (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_prices DROP FOREIGN KEY FK_86B72CFD38248176');
        $this->addSql('ALTER TABLE product_prices DROP FOREIGN KEY FK_86B72CFD98761E79');
        $this->addSql('ALTER TABLE product_prices CHANGE currency_id currency_id INT NOT NULL, CHANGE product_offer_id product_offer_id INT NOT NULL');
    }
}
