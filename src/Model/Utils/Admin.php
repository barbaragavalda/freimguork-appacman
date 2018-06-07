<?php

namespace Appacman\Model\Utils;

use Core\Model\Encryptor\TwoWay;
use Core\Model\Model;

class Admin extends Model {

    /**
     * get email addresses of administrators
     * @return array
     */
    public function getEmails(){
        $sql = '
            SELECT id_appacman_user, `name`, email, created
            FROM appacman_user
            WHERE id_appacman_user_profile = 1
        ';
        $admins = $this->mysql->query($sql);

        $to = array();
        if( count($admins) ) {
            foreach ($admins as $admin) {
                $key = $admin['id_appacman_user'] . '_' . $admin['created'] . '_';
                $to[] = array(
                    'name' => TwoWay::decrypt($admin['name'], $key . 'name'),
                    'email' => TwoWay::decrypt($admin['email'], $key . 'email'),
                );
            }
        }
        return $to;
    }

}