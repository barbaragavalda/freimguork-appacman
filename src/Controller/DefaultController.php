<?php

namespace Appacman\Controller;

class DefaultController extends AppacmanController
{

    // 404 error
    protected function run(): void
    {
        if ($this->user->loggedIn()) {
            $this->template('DefaultTemplate/loggedin.twig');
        } else {
            $this->template('DefaultTemplate/loggedout.twig');
        }
    }

    protected function hasPermission(): bool
    {
        return true;
    }

    protected function getTitle(): string
    {
        return 'Appacman';
    }

    protected function getBreadcrumb(): array
    {
        return array();
    }

}