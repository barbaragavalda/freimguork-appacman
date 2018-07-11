<?php

namespace Appacman\Model;

use Core\Model\Model;
use Core\Model\Push\Push;

class PushCronJob extends Model {

    private $systemType = null;

    public function __construct(){
        parent::__construct();

        $sql = '
            SELECT id_appacman_notification
            FROM appacman_notification
            WHERE code = "system"
        ';
        $type = $this->mysql->query($sql);
        if( count($type) ){
            $this->systemType = $type[0]['id_appacman_notification'];
        }
    }

    public function sendPending(){
        $now = date('Y-m-d H:i:s');
        $sql = '
            SELECT ap.id_appacman_push AS id, ap.*, apl.*, al.culture
            FROM appacman_push AS ap
            INNER JOIN appacman_push_lang AS apl ON ap.id_appacman_push = apl.id_appacman_push
            INNER JOIN appacman_lang AS al ON apl.id_appacman_lang = al.id_appacman_lang
            WHERE send <= :now            
        ';
        $params = array(
            'now'   => array('value' => $now,           'type' => \PDO::PARAM_STR)
        );
        $notifications = $this->mysql->query($sql, $params);

        $deleteIDs = array();
        $translatedNotifications = $this->getTranslation($notifications);
        foreach($translatedNotifications as $notification){
            $deleteIDs[] = $notification['id'];
            $devices = $this->getDevices($notification, $this->systemType);
            foreach($devices as $device){
                $message = $notification['name'][ $device['user_language'] ];
                if( $message ){
                    $push = new Push();
                    $push->send(array($device), $message, $notification['deeplink']);
                }
            }
        }

        if( count($deleteIDs) ){
            $this->delete($deleteIDs);
        }
    }

    protected function getTranslation($items){
        $translations = array();
        foreach($items as $item){
            $key = 'id_' . $item['id'];
            $translation = $item['name'];
            if( !array_key_exists($key, $translations) ){
                $translations[$key] = $item;
                $translations[$key]['name'] = array();
            }
            $translations[$key]['name'][ $item['culture'] ] = $translation;
            unset($translations[$key]['culture']);
        }

        return $translations;
    }

    protected function getDevices($info, $notificationType){
        $wheres = array();
        if( array_key_exists('platform', $info) && $info['platform'] ){
            $wheres[] = 'apd.platform IN (' . $this->getWhereIn($info['platform']) . ')';
        }
        if( array_key_exists('model', $info) && $info['model'] ){
            $wheres[] = 'apd.model IN (' . $this->getWhereIn($info['model']) . ')';
        }
        if( array_key_exists('os_version', $info) && $info['os_version'] ){
            $wheres[] = 'apd.os_version IN (' . $this->getWhereIn($info['os_version']) . ')';
        }
        if( array_key_exists('app_version', $info) && $info['app_version'] ){
            $wheres[] = 'apd.app_version IN (' . $this->getWhereIn($info['app_version']) . ')';
        }
        if( array_key_exists('last_connection', $info) && $info['last_connection'] ){
            $wheres[] = 'apd.last_connection <= "' . $info['last_connection'] . ' 23:59:59"';
        }
        $whereNoUser = $whereUser = '';
        if( count($wheres) ){
            $whereNoUser = ' AND ' . implode(' AND ', $wheres);
            $whereUser = 'WHERE ' . implode(' AND ', $wheres);
        }

        $union = '';
        if( $this->mysql->tableExists('user') ){
            $union = '
                UNION(
                    SELECT apd.token, apd.platform, u.language
                    FROM appacman_push_device AS apd
                    INNER JOIN user AS u USING(id_user)
                    INNER JOIN user_appacman_notification AS uan ON u.id_user = uan.id_user AND uan.id_appacman_notification = :type
                    ' . $whereUser . '
                )
            ';
        }

        $sql = '
            SELECT GROUP_CONCAT(DISTINCT(t.token)) AS tokens, LOWER(t.platform) AS name, t.language AS user_language
            FROM
            (
                (
                    SELECT apd.token, apd.platform,  IFNULL(apd.language,"es") AS language
                    FROM appacman_push_device AS apd
                    WHERE apd.id_user IS NULL ' . $whereNoUser . '
                )
                ' . $union . '
            )AS t
            GROUP BY platform, user_language
        ';
        $params = array(
            'type' => array('value' => $notificationType, 'type' => \PDO::PARAM_INT)
        );
        return $this->mysql->query($sql, $params);
    }

    private function getWhereIn($list){
        $array = explode(',', $list);
        $array = array_map(array($this, 'addQuotes'), $array);
        return implode(',', $array);
    }

    private function addQuotes($e){
        return '"' . $e . '"';
    }

    private function delete($ids){
        $sql = '
            DELETE FROM appacman_push
            WHERE id_appacman_push IN (' . implode(',', $ids) . ')
        ';
        $this->mysql->query($sql);
    }

}