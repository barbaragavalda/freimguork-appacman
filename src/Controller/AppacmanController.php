<?php

namespace Appacman\Controller;

use Appacman\Model\Business;
use Appacman\Model\User;
use Core\Controller\Controller;

abstract class AppacmanController extends Controller {

    /**
     * @var \Appacman\Model\User user object
     */
    private $user = null;

    public function __construct(){
        parent::__construct();

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
            $isLogedOutPage = in_array($currentPage, array(gettext('he-olvidado-mi-contrasena')) ) === true;
        }

        $this->user = User::getInstance();
        if( $this->user->loggedIn() || $isLogedOutPage ){
            $this->run();
        }else{
            $this->template('LoggedOut/signin.twig');
        }
    }

    abstract protected function run();

}