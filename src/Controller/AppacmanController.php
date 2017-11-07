<?php

namespace Appacman\Controller;

use Appacman\Model\Business;
use Appacman\Model\User;
use Core\Controller\Controller;

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
        $this->loggedOutPages = array(gettext('iniciar-sesion'), gettext('he-olvidado-mi-contrasena'));

        // domain admin css
        $this->assign('admin_domain', $this->static_domain . APPACMAN . 'public/');
        $this->assign('vendor_domain', $this->static_domain . 'vendor/almasaeed2010/adminlte/');

        //business info
        $business = new Business();
        $this->assign('business', $business->getInfo());
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
        if( $isLoggedIn && $isLogedOutPage ){
            // redirect logedin users to home page
            $this->redirect($this->domain);
        }else if( !$isLoggedIn && !$isLogedOutPage ){
            // redirect logedout users to signin page
            $this->redirect($this->domain . gettext('iniciar-sesion'), 401);
        }else{
            // execute currect page
            if( $isLoggedIn ){
                $this->assign('username', $this->user->getName());
            }
            $this->run();
        }
    }

    abstract protected function run();

}