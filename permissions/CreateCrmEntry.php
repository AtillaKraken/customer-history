<?php

namespace humhub\modules\crm\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;

class CreateCrmEntry extends BasePermission
{
    /**
     * Default: true
     */
    protected $defaultState = self::STATE_ALLOW;

    /**
     * Default: allow to all space-members
     */
    protected $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        Space::USERGROUP_MEMBER,
    ];

    /**
     * Only allow to change creation-perms for Members
     *
     * => users are forced to join Space to be able to commit
     * => members are (per default) allowed to create, but can be prohibited to doso
     */
    protected $fixedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        Space::USERGROUP_USER,
        Space::USERGROUP_GUEST,
    ];

    protected $title = 'CRM Einträge erstellen';
    protected $description = 'Erlaubt das Anlegen von neuen Kontakten, Organisationen, Events und Interaktionen.';
    protected $moduleId = 'crm';
}
