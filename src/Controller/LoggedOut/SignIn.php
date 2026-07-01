<?php

namespace Appacman\Controller\LoggedOut;

use Appacman\Controller\AppacmanController;
use Appacman\Model\LoggedOut\UserForm;

class SignIn extends AppacmanController
{

    protected function run(): void
    {
        $send = false;
        if (isset($_POST['enter'])) {
            $form = new UserForm();
            $form->signin();
            $send     = $form->getSend();
            $formInfo = $form->getForm();
            $error    = $form->getError();

            $extraUser = 'Appacman\\Model\\ExtraUser';
            if (class_exists($extraUser)) {
                $form = new $extraUser($formInfo, $error);
                if ($send) {
                    if (method_exists($form, 'extraSignin')) {
                        $form->extraSignin();
                    }
                } else {
                    if (method_exists($form, 'signin')) {
                        $form->signin();
                    }
                }
                $send = $form->getSend();

                if ($form->getError()) {
                    $formInfo = $form->getForm();
                    $error    = $form->getError();
                }
            }

            $this->assign('form', $formInfo);
            $this->assign('form_error', $error);
        }

        if ($send) {
            $this->redirect($this->domain);
        } else {
            $this->template('LoggedOut/signin.twig');
        }
    }

    protected function hasPermission(): bool
    {
        return !$this->user->loggedIn();
    }

    protected function getTitle(): string
    {
        return _('Iniciar sesión');
    }

    protected function getBreadcrumb(): array
    {
        return array();
    }

}
