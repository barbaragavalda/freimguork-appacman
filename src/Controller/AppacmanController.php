<?php

namespace Appacman\Controller;

use Appacman\Model\Business;
use Appacman\Model\User;
use Appacman\Service\NavigationAccessResolver;
use Core\Controller\CacheManager;
use Core\Controller\Controller;
use Core\Utils\Config;
use Core\Utils\Session;

abstract class AppacmanController extends Controller
{

    protected ?User $user = null;

    protected Session $session;

    private array $loggedOutPages;

    public function __construct(Config $config, CacheManager $modelCache, Session $session)
    {
        parent::__construct($config, $modelCache);

        $this->session = $session;

        // logged out pages
        $this->loggedOutPages = array(_('iniciar-sesion'), _('he-olvidado-mi-contrasena'), _('cambiar-contrasena'));

        // domain admin CSS
        $this->assign('adminDomain', $this->rootDomain . 'vendor/optisistem/freimguork-appacman/src/public/');
        $this->assign('vendorDomain', $this->rootDomain . 'vendor/almasaeed2010/adminlte/');

        //business info
        $business = new Business();
        $this->assign('business', $business->getInfo());

        // pending messages
        $this->assign('pendingMessage', $session->get('pendingMessage'));
        $session->delete('pendingMessage');
        $this->assign('pendingError', $session->get('pendingError'));
        $session->delete('pendingError');
    }

    public function build(): void
    {
        // User is appacman's own singleton, not core's - nothing registers it in the
        // container, and Bootstrap (core's composition root) has no appacman-specific
        // hook to add one, so this stays a direct call rather than a constructor param
        $this->user = User::getInstance();

        $resolver = new NavigationAccessResolver($this->user);
        $access   = $resolver->resolve($this->parts, $this->loggedOutPages, fn() => $this->hasPermission());

        if ($access->isRedirect()) {
            $this->redirect($this->domain . $access->redirectPath(), $access->redirectStatus());
            return;
        }

        $profileInfo = $this->user->getProfileInfo();
        if ($profileInfo != null && array_key_exists('logo', $profileInfo)) {
            $this->info['business']['logo'] = $profileInfo['logo'];
        }

        if ($access->isLoggedIn) {
            $this->assign('username', $this->user->getName());
        }

        $this->assign('title', $this->getTitle());
        $this->assign('menu', $access->menuItems);
        $this->assign('breadcrumb', $this->getBreadcrumb());

        $this->run();
    }

    abstract protected function run();

    abstract protected function hasPermission();

    abstract protected function getTitle();

    abstract protected function getBreadcrumb();

}
