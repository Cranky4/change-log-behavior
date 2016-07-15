<?php

    use yii\db\Migration;

    class m160501_180308_changelogs extends Migration
    {
        // Use safeUp/safeDown to run migration code within a transaction
        public function safeUp()
        {
            $this->execute("
                CREATE TABLE `changelogs` (
                    `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                    `level` INT(11) NULL DEFAULT NULL,
                    `category` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
                    `log_time` INT(11) NULL DEFAULT NULL,
                    `prefix` TEXT NULL COLLATE 'utf8_unicode_ci',
                    `message` TEXT NULL COLLATE 'utf8_unicode_ci',
                    PRIMARY KEY (`id`),
                    INDEX `idx_log_level` (`level`),
                    INDEX `idx_log_category` (`category`)
                )
                COLLATE='utf8_unicode_ci'
                ENGINE=InnoDB
                ;
            ");
        }

        public function safeDown()
        {
            $this->dropTable('changelogs');
        }
    }
