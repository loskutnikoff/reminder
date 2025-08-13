<?php

use yii\db\Migration;

class m231222_082356_create_table_dsf_reminder_template extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            'dsf_reminder_template',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull(),
                'channel_type' => $this->tinyInteger()->notNull()->defaultValue(1),
                'context' => $this->string()->notNull(),
                'message' => $this->text()->notNull(),
                'created_by' => $this->integer()->notNull(),
                'updated_by' => $this->integer()->notNull(),
                'created_at' => $this->dateTime()->notNull(),
                'updated_at' => $this->dateTime()->notNull(),
            ],
            'ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    }

    public function safeDown()
    {
        $this->dropTable('dsf_reminder_template');
    }
}
