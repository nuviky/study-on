<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220421151459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson DROP CONSTRAINT fk_f87474f34638a18f');
        $this->addSql('DROP INDEX idx_f87474f34638a18f');
        $this->addSql('ALTER TABLE lesson ADD course_id INT NOT NULL');
        $this->addSql('ALTER TABLE lesson ADD number INT NOT NULL');
        $this->addSql('ALTER TABLE lesson DROP course_relation_id');
        $this->addSql('ALTER TABLE lesson DROP lesson_number');
        $this->addSql('ALTER TABLE lesson RENAME COLUMN lesson_content TO content');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3591CC992 FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F87474F3591CC992 ON lesson (course_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE lesson DROP CONSTRAINT FK_F87474F3591CC992');
        $this->addSql('DROP INDEX IDX_F87474F3591CC992');
        $this->addSql('ALTER TABLE lesson ADD course_relation_id INT NOT NULL');
        $this->addSql('ALTER TABLE lesson ADD lesson_number INT NOT NULL');
        $this->addSql('ALTER TABLE lesson DROP course_id');
        $this->addSql('ALTER TABLE lesson DROP number');
        $this->addSql('ALTER TABLE lesson RENAME COLUMN content TO lesson_content');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT fk_f87474f34638a18f FOREIGN KEY (course_relation_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f87474f34638a18f ON lesson (course_relation_id)');
    }
}
