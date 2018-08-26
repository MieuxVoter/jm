<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180826110319 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, proposal_id INT NOT NULL, author VARCHAR(250) DEFAULT NULL, date_create DATETIME NOT NULL, date_delete DATETIME DEFAULT NULL, INDEX IDX_AB55E24FF4792058 (proposal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FF4792058 FOREIGN KEY (proposal_id) REFERENCES proposal (id)');
        $this->addSql('ALTER TABLE vote ADD participation_id INT NOT NULL, DROP author, DROP date_vote, DROP date_delete');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A1085646ACE3B73 FOREIGN KEY (participation_id) REFERENCES participation (id)');
        $this->addSql('CREATE INDEX IDX_5A1085646ACE3B73 ON vote (participation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A1085646ACE3B73');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP INDEX IDX_5A1085646ACE3B73 ON vote');
        $this->addSql('ALTER TABLE vote ADD author VARCHAR(250) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD date_vote DATETIME NOT NULL, ADD date_delete DATETIME DEFAULT NULL, DROP participation_id');
    }
}
