<?php

namespace Appacman\Controller;

use Appacman\Model\Business;
use Appacman\Model\Menu;
use Appacman\Model\User;
use Core\Controller\Controller;
use Core\Utils\Session;

abstract class AppacmanController extends Controller
{

    protected ?User $user = null;

    private array $loggedOutPages;

    public function __construct()
    {
        parent::__construct();

        // logged out pages
        $this->loggedOutPages = array(_('iniciar-sesion'), _('he-olvidado-mi-contrasena'), _('cambiar-contrasena'));

        // domain admin CSS
        $this->assign('adminDomain', $this->rootDomain . 'vendor/Optisistem/freimguork-appacman/src/public/');
        $this->assign('vendorDomain', $this->rootDomain . 'vendor/almasaeed2010/adminlte/');

        //business info
        $business = new Business();
        $this->assign('business', $business->getInfo());

        // pending messages
        $session = Session::getInstance();
        $this->assign('pendingMessage', $session->get('pendingMessage'));
        $session->delete('pendingMessage');
        $this->assign('pendingError', $session->get('pendingError'));
        $session->delete('pendingError');
    }

    public function build(): void
    {
        // do not redirect logged out pages
        $isLoggedOutPage = false;
        if (count($this->parts)) {
            $currentPage     = $this->parts[0];
            $isLoggedOutPage = in_array($currentPage, $this->loggedOutPages) === true;
        }

        $this->user = User::getInstance();
        $isLoggedIn = $this->user->loggedIn();
        if (!$isLoggedIn && !$isLoggedOutPage) {
            // redirect logged-out users to signin page
            $this->redirect($this->domain . _('iniciar-sesion'), 401);
        } else {
            $profileInfo = $this->user->getProfileInfo();
            if ($profileInfo != null && array_key_exists('logo', $profileInfo)) {
                $this->info['business']['logo'] = $profileInfo['logo'];
            }
            if ($this->hasPermission()) {
                $menu      = new Menu($this->user->getProfileInfo());
                $menuItems = $menu->get();

                if (($isLoggedIn && count($menuItems)) || !$isLoggedIn) {
                    // execute currect page
                    if ($isLoggedIn) {
                        $this->assign('username', $this->user->getName());
                    }

                    // page title
                    $this->assign('title', $this->getTitle());

                    // menu info
                    $this->assign('menu', $menuItems);
                    $this->assign('breadcrumb', $this->getBreadcrumb());

                    $this->run();
                } else {
                    $this->redirect($this->domain . _('iniciar-sesion'), 401);
                }
            } else {
                // redirect logedin users to home page
                $this->redirect($this->domain);
            }
        }
    }

    abstract protected function run();

    abstract protected function hasPermission();

    abstract protected function getTitle();

    abstract protected function getBreadcrumb();

}
