<?php

namespace humhub\modules\crm\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;

class ManageCrmData extends BasePermission
{
    /**
     * Default: false
     */
    protected $defaultState = self::STATE_DENY;

    /**
     * space admins and the owner are always allowed to edit/delete content
     *
     * goal of this perm: enable moderators and/or members to edit/delete content, if needed (concept of open CRM)
     */
    protected $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
    ];

    /**
     * Only allow to change manage-perms for Members and Moderators
     *
     * => members are (per default) prohibited to manage all CRM-Content, but can be permitted to do so
     * => total denial of management-perms for Users
     */
    protected $fixedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_USER,
    ];

    protected $title = 'CRM Daten verwalten';

    protected $description = 'Darf alle CRM-Einträge bearbeiten und löschen (Admin-Recht).';

    protected $moduleId = 'crm';
}
