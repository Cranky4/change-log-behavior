<?php

use yii\db\Migration;

class m160716_173208_changelogs extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    private $table = '{{%changelogs}}';

    public function safeUp()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey()->unsigned(),
            'relatedObjectType' => $this->string(191)->notNull(),
            'relatedObjectId' => $this->integer()->notNull(),
            'data' => $this->text(),
            'createdAt' => $this->integer(),
            'type' => $this->string(191)->null(),
            'userId' => $this->integer(),
            'hostname' => $this->string(191)
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $this->createIndex('IN_related_object_type', $this->table, 'relatedObjectType');
        $this->createIndex('IN_related_object_id', $this->table, 'relatedObjectId');
        $this->createIndex('IN_type', $this->table, 'type');
    }

    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
