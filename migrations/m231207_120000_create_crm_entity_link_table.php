<?php

use yii\db\Migration;

class m231207_120000_create_crm_entity_link_table extends Migration
{
    public function up()
    {
        $this->createTable('crm_entity_link', [
            'id' => $this->primaryKey(),
            'object_model' => $this->string(100)->notNull(), //
            'object_id' => $this->integer()->notNull(),
            'url' => $this->text()->notNull(),
            'title' => $this->string()->null(),
            'sort_order' => $this->integer()->defaultValue(0),
        ]);

        // index to find it fast
        $this->createIndex('idx-crm-link-object', 'crm_entity_link', ['object_model', 'object_id']);
    }

    public function down()
    {
        $this->dropTable('crm_entity_link');
    }
}
