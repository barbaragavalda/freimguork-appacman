<?php

namespace Appacman\Service;

use Appacman\Model\Menu;
use Appacman\Model\User;

/**
 * Resolves the login-gate/permission/menu logic that used to live inline in
 * AppacmanController::build(). $hasPermission is a closure rather than a
 * plain bool because some hasPermission() implementations (e.g. Content's)
 * run a DB query as a side effect, and must only run once the login gate has
 * already passed - same evaluation order the inline version had.
 */
final class NavigationAccessResolver
{

    public function __construct(private readonly User $user)
    {
    }

    public function resolve(array $parts, array $loggedOutPages, \Closure $hasPermission): NavigationAccess
    {
        $currentPage     = $parts[0] ?? null;
        $isLoggedOutPage = $currentPage !== null && in_array($currentPage, $loggedOutPages, true);
        $isLoggedIn      = $this->user->loggedIn();

        if (!$isLoggedIn && !$isLoggedOutPage) {
            return NavigationAccess::redirectToSignIn();
        }

        if (!$hasPermission()) {
            return NavigationAccess::redirectHome();
        }

        $menu      = new Menu($this->user->getProfileInfo());
        $menuItems = $menu->get();

        if (($isLoggedIn && count($menuItems)) || !$isLoggedIn) {
            return NavigationAccess::granted($isLoggedIn, $menuItems);
        }

        return NavigationAccess::redirectToSignIn();
    }

}
