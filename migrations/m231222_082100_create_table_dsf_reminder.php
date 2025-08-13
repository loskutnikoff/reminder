<?php

use yii\db\Migration;

class m231222_082100_create_table_dsf_reminder extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('dsf_reminder', [
            'id' => $this->primaryKey(),
            'object_type' => $this->smallInteger()->notNull(),
            'object_id' => $this->integer()->notNull(),
            'channel_type' => $this->tinyInteger()->notNull()->defaultValue(1),
            'date_send' => $this->dateTime()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ],
            'ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('dsf_reminder');
    }
}
