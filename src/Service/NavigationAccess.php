<?php

namespace Appacman\Service;

/**
 * Outcome of NavigationAccessResolver::resolve(): either a redirect (login
 * gate failed, permission denied, or the resolved menu came up empty for a
 * logged-in user) or a granted result carrying what the view needs.
 */
final class NavigationAccess
{

    private function __construct(
        private readonly bool $isRedirect,
        private readonly string $redirectPath,
        private readonly int $redirectStatus,
        public readonly bool $isLoggedIn = false,
        public readonly array $menuItems = array(),
    ) {
    }

    public static function redirectToSignIn(): self
    {
        return new self(true, _('iniciar-sesion'), 401);
    }

    public static function redirectHome(): self
    {
        return new self(true, '', 301);
    }

    public static function granted(bool $isLoggedIn, array $menuItems): self
    {
        return new self(false, '', 0, $isLoggedIn, $menuItems);
    }

    public function isRedirect(): bool
    {
        return $this->isRedirect;
    }

    public function redirectPath(): string
    {
        return $this->redirectPath;
    }

    public function redirectStatus(): int
    {
        return $this->redirectStatus;
    }

}
