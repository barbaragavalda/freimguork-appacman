<?php

namespace Appacman\Model;

use Core\Model\File;
use Core\Model\Model;
use Core\Model\Push\Push;
use PDO;

class PushCronJob extends Model
{

    protected ?int $systemType = null;

    public function __construct()
    {
        parent::__construct();

        $sql  = '
            SELECT id_appacman_notification
            FROM appacman_notification
            WHERE code = "system"
        ';
        $type = $this->mysql->query($sql);
        if (count($type)) {
            $this->systemType = $type[0]['id_appacman_notification'];
        }
    }

    protected function getPending(): array
    {
        $where = '';
        if ($this->mysql->fieldExists('appacman_push', 'is_sent')) {
            $where = ' AND is_sent = 0';
        }

        $now    = date('Y-m-d H:i:s');
        $sql    = '
            SELECT ap.id_appacman_push AS id, ap.*, apl.*, al.culture
            FROM appacman_push AS ap
            INNER JOIN appacman_push_lang AS apl ON ap.id_appacman_push = apl.id_appacman_push
            INNER JOIN appacman_lang AS al ON apl.id_appacman_lang = al.id_appacman_lang
            WHERE send <= :now ' . $where . '
        ';
        $params = array(
            'now' => array('value' => $now, 'type' => PDO::PARAM_STR)
        );
        return $this->mysql->query($sql, $params);
    }

    public function sendPending(): void
    {
        $notifications = $this->getPending();

        $deleteIDs               = array();
        $translatedNotifications = $this->getTranslation($notifications);
        foreach ($translatedNotifications as $notification) {
            $deleteIDs[] = $notification;
        }

        if (count($deleteIDs)) {
            if (array_key_exists('is_sent', $deleteIDs[0])) {
                $this->markAsSent($deleteIDs);
            } else {
                $this->delete($deleteIDs);
            }
        }

        foreach ($translatedNotifications as $notification) {
            $devices = $this->getDevices($notification, $this->systemType);
            foreach ($devices as $device) {
                $message = $notification['name'][ $device['user_language'] ];
                if ($message) {
                    $push = new Push();
                    $push->send(array($device), $message, $notification);
                }
            }
        }
    }

    protected function getTranslation($items): array
    {
        $translations = array();
        foreach ($items as $item) {
            $key         = 'id_' . $item['id'];
            $translation = $item['name'];
            if (!array_key_exists($key, $translations)) {
                $translations[ $key ]         = $item;
                $translations[ $key ]['name'] = array();
            }
            $translations[ $key ]['name'][ $item['culture'] ] = $translation;
            unset($translations[ $key ]['culture']);
        }

        return $translations;
    }

    protected function getFilters($info): array
    {
        $wheres = array();
        if (array_key_exists('platform', $info) && $info['platform']) {
            $wheres[] = 'apd.platform IN (' . $this->getWhereIn($info['platform']) . ')';
        }
        if (array_key_exists('model', $info) && $info['model']) {
            $wheres[] = 'apd.model IN (' . $this->getWhereIn($info['model']) . ')';
        }
        if (array_key_exists('os_version', $info) && $info['os_version']) {
            $wheres[] = 'apd.os_version IN (' . $this->getWhereIn($info['os_version'], 'addQuotes') . ')';
        }
        if (array_key_exists('app_version', $info) && $info['app_version']) {
            $wheres[] = 'apd.app_version IN (' . $this->getWhereIn($info['app_version'], 'addQuotes') . ')';
        }
        if (array_key_exists('last_connection', $info) && $info['last_connection']) {
            $wheres[] = 'apd.last_connection <= "' . $info['last_connection'] . ' 23:59:59"';
        }

        return array(
            'hasSql'        => true,
            'params'        => array(),
            'where'         => $wheres,
            'whereUser'     => $wheres,
            'innerJoin'     => '',
            'innerJoinUser' => ''
        );
    }

    protected function getDevices($info, $notificationType): array
    {
        $filters       = $this->getFilters($info);
        $params        = $filters['params'];
        $innerJoin     = $filters['innerJoin'];
        $innerJoinUser = $filters['innerJoinUser'];

        $whereNoUser = $whereUser = '';
        if (count($filters['where'])) {
            $whereNoUser = ' AND ' . implode(' AND ', $filters['where']);
        }
        if (count($filters['whereUser'])) {
            $whereUser = 'WHERE ' . implode(' AND ', $filters['whereUser']);
        }

        $union = array();
        if ($filters['hasSql']) {
            $union[] = '
                (
                    SELECT apd.token, apd.platform, IFNULL(apd.language,"es") AS language
                    FROM appacman_push_device AS apd
                    ' . $innerJoin . '
                    WHERE (apd.id_user IS NULL OR apd.id_user <= 0) ' . $whereNoUser . '
                )
            ';
        }
        if ($this->mysql->tableExists('user')) {
            $fieldLang = '"es" AS language';
            if ($this->mysql->fieldExists('user', 'language')) {
                $fieldLang = 'u.language';
            }
            $union[] = '
                (
                    SELECT apd.token, apd.platform, ' . $fieldLang . '
                    FROM appacman_push_device AS apd
                    INNER JOIN user AS u USING(id_user)
                    INNER JOIN user_appacman_notification AS uan ON u.id_user = uan.id_user AND uan.id_appacman_notification = :type
                    ' . $innerJoinUser . '
                    ' . $whereUser . '
                )
            ';
        }

        $sql            = '
            SELECT GROUP_CONCAT(DISTINCT(t.token)) AS tokens, LOWER(t.platform) AS name, t.language AS user_language
            FROM (' . implode('UNION', $union) . ')AS t
            GROUP BY platform, user_language
        ';
        $params['type'] = array('value' => $notificationType, 'type' => PDO::PARAM_INT);
        return $this->mysql->query($sql, $params);
    }

    private function getWhereIn($list, $function = 'addQuotesReplace'): string
    {
        $array = explode(',', $list);
        $array = array_map(array($this, $function), $array);
        return implode(',', $array);
    }

    private function addQuotes($e): string
    {
        return '"' . $e . '"';
    }

    private function addQuotesReplace($e): string
    {
        return '"' . str_replace('.', ',', $e) . '"';
    }

    private function delete($notifications): void
    {
        if (count($notifications)) {
            $ids = array_column($notifications, 'id');
            $sql = '
                DELETE FROM appacman_push
                WHERE id_appacman_push IN (' . implode(',', $ids) . ')
            ';
            $this->mysql->query($sql);

            if (array_key_exists('image', $notifications[0])) {
                $images = array_column($notifications, 'image');
                foreach ($images as $fileID) {
                    $file = new File($fileID);
                    $file->deleteFromFileTable();
                    $file->deleteFromDisk();
                }
            }
        }
    }

    protected function markAsSent($notifications): void
    {
        if (count($notifications)) {
            $ids = array_column($notifications, 'id');
            $sql = '
                UPDATE appacman_push
                SET is_sent = 1
                WHERE id_appacman_push IN (' . implode(',', $ids) . ')
            ';
            $this->mysql->query($sql);
        }
    }

}