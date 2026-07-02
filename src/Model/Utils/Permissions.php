<?php

namespace Appacman\Model\Utils;

use Core\Model\Model;
use PDO;

class Permissions extends Model
{

    const string  CREATE           = 'create';
    const string  EDIT             = 'edit';
    const string  DELETE           = 'delete';
    const string  SEE              = 'see';
    const string  EXPORT           = 'export';
    const string  LOCK             = 'lock';
    const string  OWN              = 'own';
    const string  DUPLICATE        = 'duplicate';
    const string  FIREBASE         = 'firebase';
    const string  SEND_CHANGES     = 'send-changes';
    const string  LOG_OUT          = 'log-out';
    const string  GENERATE_INVOICE = 'generate-invoice';

    private ?int $userID;

    private ?int $profileID;

    private array $permissionsCodes;

    private array $permissions = array();

    public function __construct($userID, $profileID = null)
    {
        parent::__construct();

        $this->permissionsCodes = array(
            self::CREATE,
            self::DELETE,
            self::EDIT,
            self::SEE,
            self::EXPORT,
            self::LOCK,
            self::OWN,
            self::DUPLICATE,
            self::SEND_CHANGES,
            self::LOG_OUT,
            self::GENERATE_INVOICE
        );
        $this->userID           = $userID;
        $this->profileID        = $profileID;
    }

    public function getProfileID(): ?int
    {
        return $this->profileID;
    }

    public function load(): void
    {
        $sql    = '
            SELECT aupp.id_appacman_content, aup.code, aupl.name, aupp.id_appacman_user_profile
            FROM appacman_user AS au
            INNER JOIN appacman_user_profile_permission AS aupp ON aupp.id_appacman_user_profile = au.id_appacman_user_profile
            INNER JOIN appacman_user_permission AS aup ON aup.id_appacman_user_permission = aupp.id_appacman_user_permission
            INNER JOIN appacman_user_permission_lang AS aupl ON aupl.id_appacman_user_permission = aup.id_appacman_user_permission AND aupl.id_appacman_lang = :lang
            WHERE au.id_appacman_user = :user_id
        ';
        $params = array(
            'user_id' => array('value' => $this->userID, 'type' => PDO::PARAM_INT),
            'lang'    => array('value' => $this->langID, 'type' => PDO::PARAM_INT)
        );
        if ($this->profileID != null) {
            $sql    = '
                SELECT aupp.id_appacman_content, aup.code, aupl.name, aupp.id_appacman_user_profile
                FROM appacman_user_profile_permission AS aupp
                INNER JOIN appacman_user_permission AS aup ON aup.id_appacman_user_permission = aupp.id_appacman_user_permission
                INNER JOIN appacman_user_permission_lang AS aupl ON aupl.id_appacman_user_permission = aup.id_appacman_user_permission AND aupl.id_appacman_lang = :lang
                WHERE aupp.id_appacman_user_profile = :profile_id
            ';
            $params = array(
                'profile_id' => array('value' => $this->profileID, 'type' => PDO::PARAM_INT),
                'lang'       => array('value' => $this->langID, 'type' => PDO::PARAM_INT)
            );
        }
        $permissions = $this->mysql->query($sql, $params);

        $this->permissions = array();
        if (count($permissions)) {
            $this->profileID = $permissions[0]['id_appacman_user_profile'];
        }
        foreach ($permissions as $permission) {
            $this->permissions[ 'c' . $permission['id_appacman_content'] ][] = array(
                'code' => $permission['code'],
                'name' => $permission['name']
            );
        }
    }

    public function getContentPermissions(int $contentID): array
    {
        $permissions = array();
        foreach ($this->permissionsCodes as $permission) {
            if (($contentPermission = $this->hasPermission($contentID, $permission)) !== false) {
                $permissions[] = $contentPermission;
            }
        }

        return $permissions;
    }

    public function hasPermission($contentID, $permission): bool|array
    {
        $contentID = 'c' . $contentID;
        if (array_key_exists($contentID, $this->permissions)) {
            foreach ($this->permissions[ $contentID ] as $contentPermission) {
                if (in_array($permission, $contentPermission)) {
                    return $contentPermission;
                }
            }
        }

        return false;
    }

}