<?php

use yii\db\Migration;

class m160716_173208_changelogs extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `{{%changelogs}}` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `relatedObjectType` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
              `relatedObjectId` int(11) unsigned DEFAULT NULL,
              `data` text COLLATE utf8mb4_unicode_ci,
              `createdAt` int(11) unsigned DEFAULT NULL,
              `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `descr` varchar(10000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `userId` int(11) DEFAULT NULL,
              `hostname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `relatedObjectName` (`relatedObjectType`,`relatedObjectId`)
            ) ENGINE=InnoDB AUTO_INCREMENT=738 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function safeDown()
    {
        $this->dropTable('{{%changelogs}}');
    }
}
