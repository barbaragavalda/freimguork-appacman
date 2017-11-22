<?php

namespace Appacman\Controller;

use Appacman\Model\Business;
use Appacman\Model\Menu;
use Appacman\Model\User;
use Core\Controller\Controller;
use Core\Utils\Session;

abstract class AppacmanController extends Controller {

    /**
     * @var \Appacman\Model\User user object
     */
    protected $user = null;

    /**
     * @var array $loggedOutPages. List of pages without login
     */
    private $loggedOutPages = array();

    public function __construct(){
        parent::__construct();

        // logged out pages
        $this->loggedOutPages = array(gettext('iniciar-sesion'), gettext('he-olvidado-mi-contrasena'), gettext('cambiar-contrasena'));

        // domain admin css
        $this->assign('admin_domain', $this->static_domain . APPACMAN . 'public/');
        $this->assign('vendor_domain', $this->static_domain . 'vendor/almasaeed2010/adminlte/');

        //business info
        $business = new Business();
        $this->assign('business', $business->getInfo());

        // pending messages
        $session = Session::getInstance();
        $this->assign('pendingMessage', $session->get('pendingMessage'));
        $session->delete('pendingMessage');
    }

    public function build(){
        // do not redirect logged out pages
        $isLogedOutPage = false;
        if( count($this->parts) ){
            $currentPage = $this->parts[0];
            $isLogedOutPage = in_array($currentPage, $this->loggedOutPages ) === true;
        }

        $this->user = User::getInstance();
        $isLoggedIn = $this->user->loggedIn();
        if( !$isLoggedIn && !$isLogedOutPage ){
            // redirect logedout users to signin page
            $this->redirect($this->domain . gettext('iniciar-sesion'), 401);
        }else{
            if( $this->hasPermission() ){
                // execute currect page
                if( $isLoggedIn ){
                    $this->assign('username', $this->user->getName());
                }

                // page title
                $this->assign('title', $this->getTitle());

                // menu info
                $menu = new Menu();
                $this->assign('menu', $menu->get());
                $this->assign('breadcrumb', $this->getBreadcrumb());

                $this->run();
            }else{
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