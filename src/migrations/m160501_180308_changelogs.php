<?php

    use yii\db\Migration;

    class m160715_173208_changelogs extends Migration
    {
        // Use safeUp/safeDown to run migration code within a transaction
        public function safeUp()
        {
            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            }
            $this->createTable('{{%changelogs}}', [
                'id'       => $this->primaryKey(),
                'level'    => $this->integer(11)->null(),
                'category' => $this->string(255)->null(),
                'log_time' => $this->integer(11),
                'prefix'   => $this->text(),
                'message'  => $this->text(),
            ], $tableOptions);

            $this->createIndex('idx_log_level', '{{%changelogs}}', ['level']);
            $this->createIndex('idx_log_category', '{{%changelogs}}', ['category']);
        }

        public function safeDown()
        {
            $this->dropTable('{{%changelogs}}');
        }
    }
