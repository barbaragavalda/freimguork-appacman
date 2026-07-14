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
 *
 * Types are array|bool, not bool: User::hasPermission() (via
 * Permissions::hasPermission()) returns the matched permission's own array of
 * details when granted, and false otherwise - never a plain bool. Callers
 * only ever use these truthily (if-checks, Twig assigns), same as before.
 */
final class CrudPermissions
{

    private function __construct(
        public readonly array|bool $canSee,
        public readonly array|bool $canEdit,
        public readonly array|bool $canCreate,
        public readonly array|bool $canDelete,
        public readonly array|bool $canOwn,
        public readonly array|bool $canLock,
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
