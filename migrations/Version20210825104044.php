<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210825104044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, height VARCHAR(255) DEFAULT NULL, weight VARCHAR(255) DEFAULT NULL, age VARCHAR(255) NOT NULL, long_sm VARCHAR(255) DEFAULT NULL, diameter VARCHAR(255) DEFAULT NULL, from_heigh VARCHAR(255) DEFAULT NULL, to_height VARCHAR(255) DEFAULT NULL, from_weight VARCHAR(255) DEFAULT NULL, to_weight VARCHAR(255) DEFAULT NULL, from_age VARCHAR(255) DEFAULT NULL, to_age VARCHAR(255) DEFAULT NULL, from_long_sm VARCHAR(255) DEFAULT NULL, to_long_sm VARCHAR(255) DEFAULT NULL, from_diameter VARCHAR(255) DEFAULT NULL, to_diameter VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, search_gender VARCHAR(255) DEFAULT NULL, orientation VARCHAR(255) DEFAULT NULL, search_orientation VARCHAR(255) DEFAULT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
    }
}
