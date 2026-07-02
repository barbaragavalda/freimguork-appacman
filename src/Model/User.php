<?php

namespace Appacman\Model;

use Appacman\Model\Utils\Permissions;
use Core\Model\Encryptor\TwoWay;
use Core\Model\Model;
use Core\Utils\Session;
use PDO;

class User extends Model
{
    private static User $instance;

    private ?array $profileInfo;

    private ?Permissions $permissions = null;

    private Session $session;

    /**
     * load user info from session
     */
    private function __construct()
    {
        parent::__construct();

        $this->session     = Session::getInstance();
        $this->id          = $this->session->get('user_id');
        $this->profileInfo = $this->session->get('profile_info');

        $this->loadPermissions();
    }

    public static function getInstance(): User
    {
        if (self::$instance === null) {
            self::$instance = new User();
        }
        return self::$instance;
    }

    public function getName(): string
    {
        return $this->session->get('user_name');
    }

    public function getProfileID(): int
    {
        return $this->permissions->getProfileID();
    }

    public function getProfileInfo(): ?array
    {
        return $this->profileInfo;
    }

    public function getEmail(): string
    {
        $sql    = '
            SELECT id_appacman_user, email, created
            FROM appacman_user
            WHERE id_appacman_user = :id
        ';
        $params = array(
            'id' => array('value' => $this->id, 'type' => PDO::PARAM_INT)
        );
        $user   = $this->mysql->query($sql, $params);
        if (count($user)) {
            $user          = $user[0];
            $this->id      = $user['id_appacman_user'];
            $this->created = $user['created'];
            $this->setKey();
            return TwoWay::decrypt($user['email'], $this->key . 'email');
        }
        return '';
    }

    public function loggedIn(): bool
    {
        if (empty($this->id)) {
            return false;
        }
        return true;
    }

    public function logout(): void
    {
        $this->session->clear();
    }

    public function signin(int $userID, string $username, ?array $profileInfo = null): void
    {
        $this->id = $userID;
        $this->session->set('user_id', $userID);
        $this->session->set('user_name', $username);
        if ($profileInfo != null) {
            $this->session->set('profile_info', $profileInfo);
        }
    }

    private function loadPermissions(): void
    {
        $profileID = null;
        if ($this->profileInfo != null) {
            $profileID = $this->profileInfo['profile'];
        }
        $this->permissions = new Permissions($this->id, $profileID);
        $this->permissions->load();
    }

    public function getContentPermissions($contentID): array
    {
        return $this->permissions->getContentPermissions($contentID);
    }

    public function hasPermission($contentID, $permission): array
    {
        return $this->permissions->hasPermission($contentID, $permission);
    }

}