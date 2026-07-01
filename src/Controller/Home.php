<?php
/**
 * Created by PhpStorm.
 * User: barbaragavaldabalada
 * Date: 6/11/17
 * Time: 15:34
 */

namespace Appacman\Controller;

class Home extends AppacmanController
{

    protected function run(): void
    {
        $this->template('home.twig');
    }

    protected function hasPermission(): bool
    {
        return $this->user->loggedIn();
    }

    protected function getTitle(): string
    {
        return _('Inicio');
    }

    protected function getBreadcrumb(): array
    {
        return array();
    }

}