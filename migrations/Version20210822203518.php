<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210822203518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, picture VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_quote (category_id INT NOT NULL, quote_id INT NOT NULL, INDEX IDX_834284BD12469DE2 (category_id), INDEX IDX_834284BDDB805178 (quote_id), PRIMARY KEY(category_id, quote_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_quote ADD CONSTRAINT FK_834284BD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_quote ADD CONSTRAINT FK_834284BDDB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_quote DROP FOREIGN KEY FK_834284BD12469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_quote');
    }
}
