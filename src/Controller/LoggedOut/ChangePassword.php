<?php

namespace Appacman\Controller\LoggedOut;

use Appacman\Controller\AppacmanController;
use Appacman\Model\LoggedOut\UserForm;

class ChangePassword extends AppacmanController
{

    protected function run(): void
    {
        $send = false;
        $form = new UserForm();
        $hash = $this->getParam('hash');

        if ($form->canChange($hash)) {
            if (isset($_POST['change'])) {
                $form->change();

                $send = $form->getSend();
                $this->assign('form_error', $form->getError());
            }
        } else {
            $this->assign('wrong_link', true);
        }

        $this->assign('form_send', $send);
        $this->template('LoggedOut/change-password.twig');
    }

    protected function hasPermission(): bool
    {
        return !$this->user->loggedIn();
    }

    protected function getTitle(): string
    {
        return _('Cambiar contraseña');
    }

    protected function getBreadcrumb(): array
    {
        return array();
    }
}