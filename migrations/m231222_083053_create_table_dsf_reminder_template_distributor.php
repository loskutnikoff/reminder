<?php

use yii\db\Migration;

class m231222_083053_create_table_dsf_reminder_template_distributor extends Migration
{
    public function safeUp()
    {
        $this->createTable('dsf_reminder_template_distributor', [
            'template_id' => $this->integer()->notNull(),
            'distributor_id' => $this->integer()->notNull(),
            'PRIMARY KEY(template_id, distributor_id)',
        ], 'ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        $this->addForeignKey(
            'fk-reminder_template_distributor_template_id',
            'dsf_reminder_template_distributor',
            'template_id',
            'dsf_reminder_template',
            'id'
        );
        $this->addForeignKey(
            'fk-reminder_template_distributor_distributor_id',
            'dsf_reminder_template_distributor',
            'distributor_id',
            'dsf_distributor',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropTable('dsf_reminder_template_distributor');
    }
}
