<?php
/**
 * Created by PhpStorm.
 * User: barbaragavaldabalada
 * Date: 31/10/17
 * Time: 21:02
 */

namespace Appacman\Model;

use Appacman\Model\Utils\Permissions;
use Core\Utils\Session;

class User {

    /**
     * @var \Appacman\Model\User $instance. Instance of the singleton
     */
    private static $instance;

    /**
     * @var int $id. User id
     */
    private $id = null;

    /**
     * @var \Appacman\Model\Utils\Permissions $permissions. User permissions
     */
    private $permissions = null;

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

        $this->loadPermissions();
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

    /**
     * @return string username
     */
    public function getName(){
        return $this->session->get('user_name');
    }

    /**
     * is the user loggedin
     * @return bool
     */
    public function loggedIn(){
        if( empty($this->id) ){
            return false;
        }
        return true;
    }

    /**
     * remove session
     */
    public function logout(){
        $this->session->clear();
    }

    /**
     * save session
     * @param $userID
     * @param $username
     */
    public function signin($userID, $username){
        $this->id = $userID;
        $this->session->set('user_id', $userID);
        $this->session->set('user_name', $username);
    }

    /**
     * load user permissions
     */
    private function loadPermissions(){
        $this->permissions = new Permissions($this->id);
        $this->permissions->load();
    }

    public function getContentPermissions($contentID){
        return $this->permissions->getContentPermissions($contentID);
    }

    public function hasPermission($contentID, $permission){
        return $this->permissions->hasPermission($contentID, $permission);
    }

}