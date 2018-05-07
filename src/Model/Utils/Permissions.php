<?php

namespace Appacman\Model\Utils;

use Core\Model\Model;

class Permissions extends Model {

    const CREATE = 'create';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const SEE = 'see';
    const EXPORT = 'export';
    const LOCK = 'lock';
    const OWN = 'own';

    /**
     * @var int $userID
     */
    private $userID = 0;

    /**
     * @var int $profileID
     */
    private $profileID = null;

    /**
     * @var array $permissionsCodes
     */
    private $permissionsCodes = array();

    /**
     * @var array $permissions
     */
    private $permissions = array();

    public function __construct($userID, $profileID = null){
        parent::__construct();

        $this->permissionsCodes = array(self::CREATE, self::DELETE, self::EDIT, self::SEE, self::EXPORT, self::LOCK, self::OWN);
        $this->userID = $userID;
        $this->profileID = $profileID;
    }

    public function load(){
        $sql = '
            SELECT aupp.id_appacman_content, aup.code, aupl.name
            FROM appacman_user AS au
            INNER JOIN appacman_user_profile_permission AS aupp ON aupp.id_appacman_user_profile = au.id_appacman_user_profile
            INNER JOIN appacman_user_permission AS aup ON aup.id_appacman_user_permission = aupp.id_appacman_user_permission
            INNER JOIN appacman_user_permission_lang AS aupl ON aupl.id_appacman_user_permission = aup.id_appacman_user_permission AND aupl.id_appacman_lang = :lang
            WHERE au.id_appacman_user = :user_id
        ';
        $params = array(
            'user_id'   => array('value' => $this->userID, 'type' => \PDO::PARAM_INT),
            'lang'      => array('value' => $this->langID, 'type' => \PDO::PARAM_INT)
        );
        if( $this->profileID != null ){
            $sql = '
                SELECT aupp.id_appacman_content, aup.code, aupl.name
                FROM appacman_user_profile_permission AS aupp
                INNER JOIN appacman_user_permission AS aup ON aup.id_appacman_user_permission = aupp.id_appacman_user_permission
                INNER JOIN appacman_user_permission_lang AS aupl ON aupl.id_appacman_user_permission = aup.id_appacman_user_permission AND aupl.id_appacman_lang = :lang
                WHERE aupp.id_appacman_user_profile = :profile_id
            ';
            $params = array(
                'profile_id'    => array('value' => $this->profileID,   'type' => \PDO::PARAM_INT),
                'lang'          => array('value' => $this->langID,      'type' => \PDO::PARAM_INT)
            );
        }
        $permissions = $this->mysql->query($sql, $params);

        $this->permissions = array();
        foreach($permissions as $permission){
            $this->permissions['c'.$permission['id_appacman_content']][] = array(
                'code' => $permission['code'],
                'name' => $permission['name']
            );
        }
    }

    public function getContentPermissions($contentID){
        $permissions = array();
        foreach($this->permissionsCodes as $permission){
            if( ($contentPermission = $this->hasPermission($contentID, $permission)) !== false ){
                $permissions[] = $contentPermission;
            }
        }

        return $permissions;
    }

    public function hasPermission($contentID, $permission){
        $contentID = 'c'.$contentID;
        if( array_key_exists($contentID, $this->permissions) ){
            foreach($this->permissions[$contentID] as $contentPermission){
                if( array_search($permission, $contentPermission) !== false ){
                    return $contentPermission;
                }
            }
        }

        return false;
    }

}