<?php

namespace Appacman\Service;

use Appacman\Model\User;
use Appacman\Model\Utils\Permissions;

/**
 * The 6 permission checks duplicated identically in both
 * BaseContentList::hasPermission() and BaseContentForm::hasPermission().
 * Each caller still computes whatever extra, non-shared permissions it needs
 * (canExport/canDuplicate/canLogOut for lists, canSendChanges/canGenerateInvoice
 * for forms) directly against $this->user - only the common subset lives here.
 */
final class CrudPermissions
{

    private function __construct(
        public readonly bool $canSee,
        public readonly bool $canEdit,
        public readonly bool $canCreate,
        public readonly bool $canDelete,
        public readonly bool $canOwn,
        public readonly bool $canLock,
    ) {
    }

    public static function resolve(User $user, int $contentID): self
    {
        return new self(
            $user->hasPermission($contentID, Permissions::SEE),
            $user->hasPermission($contentID, Permissions::EDIT),
            $user->hasPermission($contentID, Permissions::CREATE),
            $user->hasPermission($contentID, Permissions::DELETE),
            $user->hasPermission($contentID, Permissions::OWN),
            $user->hasPermission($contentID, Permissions::LOCK),
        );
    }

}
