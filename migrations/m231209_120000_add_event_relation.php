<?php

use yii\db\Migration;

class m231209_120000_add_event_relation extends Migration
{
    public function up()
    {
        // Add event_id to interaction
        $this->addColumn('crm_interaction', 'event_id', $this->integer()->null());
        $this->createIndex('idx-interaction-event', 'crm_interaction', 'event_id');
        $this->addForeignKey('fk-interaction-event', 'crm_interaction', 'event_id', 'crm_event', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk-interaction-event', 'crm_interaction');
        $this->dropColumn('crm_interaction', 'event_id');
    }
}
