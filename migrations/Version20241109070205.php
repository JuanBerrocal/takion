<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241109070205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tkpost (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, subject_id INT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_B45D1BB9F675F31B (author_id), INDEX IDX_B45D1BB923EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tkpost ADD CONSTRAINT FK_B45D1BB9F675F31B FOREIGN KEY (author_id) REFERENCES tkuser (id)');
        $this->addSql('ALTER TABLE tkpost ADD CONSTRAINT FK_B45D1BB923EDC87 FOREIGN KEY (subject_id) REFERENCES tktopic (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tkpost DROP FOREIGN KEY FK_B45D1BB9F675F31B');
        $this->addSql('ALTER TABLE tkpost DROP FOREIGN KEY FK_B45D1BB923EDC87');
        $this->addSql('DROP TABLE tkpost');
    }
}
