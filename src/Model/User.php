<?php
/**
 * Created by PhpStorm.
 * User: barbaragavaldabalada
 * Date: 31/10/17
 * Time: 21:02
 */

namespace Appacman\Model;

use Core\Utils\Session;

class User {

    /**
     * @var \Appacman\Model\User $instance.  Instance of the singleton
     */
    private static $instance;

    private $id = null;

    /**
     * @var \Core\Utils\Session $session
     */
    private $session = null;

    /**
     * load user info from session
     */
    private function __construct(){
        $this->session = Session::getInstance();
        $this->id = $this->session->get('user_id');
    }

    /**
     * initializes the instance (if needed) based on the singleton pattern
     * @return \Appacman\Model\User
     */
    public static function getInstance(){
        if( self::$instance === null) {
            self::$instance = new User();
        }
        return self::$instance;
    }

    public function loggedIn(){
        if( empty($this->id) ){
            return false;
        }
        return true;
    }

    public function logout(){
        $this->session->clear();
    }

    public function getName(){
        return 'Bàrbara Gavaldà';
    }

    public function signin($userID){
        $this->id = $this->session->set('user_id', $userID);
    }

}