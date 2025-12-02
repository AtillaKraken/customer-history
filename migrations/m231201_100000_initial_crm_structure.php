<?php

use humhub\components\Migration;

class m231201_100000_initial_crm_structure extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // --- A. Organization ---
        $this->createTable('crm_organization', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'category' => $this->string(100)->notNull(), // Enum handling in model
            'industry' => $this->string(100),
            'size' => $this->string(50),
            'city' => $this->string(100),
            'notes' => $this->text(),
        ], $tableOptions);

        // --- B. Contact ---
        $this->createTable('crm_contact', [
            'id' => $this->primaryKey(),
            'organization_id' => $this->integer()->notNull(),
            'name' => $this->string(255), // Optional because of DSGVO
            'roles' => $this->text()->notNull(), // save as JSON or serialized String
            'gender' => $this->string(20),
            'email' => $this->string(255),
            'phone_number' => $this->string(100),
            'note' => $this->text(),
        ], $tableOptions);

        // FK Contact -> Organization
        $this->addForeignKey('fk-contact-org', 'crm_contact', 'organization_id', 'crm_organization', 'id', 'CASCADE', 'CASCADE');

        // --- C. Interaction ---
        $this->createTable('crm_interaction', [
            'id' => $this->primaryKey(),
            'date' => $this->date()->notNull(),
            'time' => $this->time(),
            'title' => $this->string(255)->notNull(),
            'channel' => $this->string(50), // Enum handled in Model
            'status' => $this->string(50)->defaultValue('PLANNED'),
            'description' => $this->text(),
            'result' => $this->text(),
            'links' => $this->text(), // JSON storage for List<text>
        ], $tableOptions);

        // --- D. Event ---
        $this->createTable('crm_event', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'date' => $this->date()->notNull(),
            'time' => $this->time(),
            'type' => $this->string(100),
            'description' => $this->text(),
            'links' => $this->text(), // JSON
            'calendar_entry_id' => $this->integer(), // bridge to calendar-module | Event <=> Calendar-Entry
        ], $tableOptions);

        // ==========================================
        // JOIN TABLES (N:M Relations)
        // ==========================================

        // 1. Organization <-> HumHub User (users responsible for an organization)
        $this->createTable('crm_organization_user', [
            'organization_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('pk-org-user', 'crm_organization_user', ['organization_id', 'user_id']);
        $this->addForeignKey('fk-org-user-org', 'crm_organization_user', 'organization_id', 'crm_organization', 'id', 'CASCADE');
        $this->addForeignKey('fk-org-user-user', 'crm_organization_user', 'user_id', 'user', 'id', 'CASCADE');

        // 2. Interaction <-> Contact
        $this->createTable('crm_interaction_contact', [
            'interaction_id' => $this->integer()->notNull(),
            'contact_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('pk-int-contact', 'crm_interaction_contact', ['interaction_id', 'contact_id']);
        $this->addForeignKey('fk-int-contact-int', 'crm_interaction_contact', 'interaction_id', 'crm_interaction', 'id', 'CASCADE');
        $this->addForeignKey('fk-int-contact-cont', 'crm_interaction_contact', 'contact_id', 'crm_contact', 'id', 'CASCADE');

        // 3. Interaction <-> Organization (Persistency-Backup! | necessary to keep related organization saved, if a contact gets deleted)
        $this->createTable('crm_interaction_organization', [
            'interaction_id' => $this->integer()->notNull(),
            'organization_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('pk-int-org', 'crm_interaction_organization', ['interaction_id', 'organization_id']);
        $this->addForeignKey('fk-int-org-int', 'crm_interaction_organization', 'interaction_id', 'crm_interaction', 'id', 'CASCADE');
        $this->addForeignKey('fk-int-org-org', 'crm_interaction_organization', 'organization_id', 'crm_organization', 'id', 'CASCADE');

        // 'SET NULL' instead of 'CASCADE' to keep Interactions if an Organization gets deleted

        // 4. Interaction <-> HumHub User (responsible Users)
        $this->createTable('crm_interaction_responsible_user', [
            'interaction_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('pk-int-resp', 'crm_interaction_responsible_user', ['interaction_id', 'user_id']);
        $this->addForeignKey('fk-int-resp-int', 'crm_interaction_responsible_user', 'interaction_id', 'crm_interaction', 'id', 'CASCADE');
        $this->addForeignKey('fk-int-resp-user', 'crm_interaction_responsible_user', 'user_id', 'user', 'id', 'CASCADE');

        // 5. Event <-> Contact
        $this->createTable('crm_event_contact', [
            'event_id' => $this->integer()->notNull(),
            'contact_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('pk-evt-contact', 'crm_event_contact', ['event_id', 'contact_id']);
        $this->addForeignKey('fk-evt-contact-evt', 'crm_event_contact', 'event_id', 'crm_event', 'id', 'CASCADE');
        $this->addForeignKey('fk-evt-contact-cont', 'crm_event_contact', 'contact_id', 'crm_contact', 'id', 'CASCADE');

        // 6. Event <-> Organization
        $this->createTable('crm_event_organization', [
            'event_id' => $this->integer()->notNull(),
            'organization_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('pk-evt-org', 'crm_event_organization', ['event_id', 'organization_id']);
        $this->addForeignKey('fk-evt-org-evt', 'crm_event_organization', 'event_id', 'crm_event', 'id', 'CASCADE');
        $this->addForeignKey('fk-evt-org-org', 'crm_event_organization', 'organization_id', 'crm_organization', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('crm_interaction_responsible_user');
        $this->dropTable('crm_interaction_organization');
        $this->dropTable('crm_interaction_contact');
        $this->dropTable('crm_organization_user');
        $this->dropTable('crm_event_contact');
        $this->dropTable('crm_event_organization');

        $this->dropTable('crm_event');
        $this->dropTable('crm_interaction');
        $this->dropTable('crm_contact');
        $this->dropTable('crm_organization');
    }
}
