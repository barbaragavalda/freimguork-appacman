<?php

namespace Appacman\Model\Push;

use Appacman\Model\PushCronJob;
use Core\Model\Push\Push;
use PDO;

class Notifier extends PushCronJob
{

    public function send(): void
    {
        $notifications           = $this->getPending();
        $translatedNotifications = $this->getTranslation($notifications);

        $deleteIDs = array();
        foreach ($translatedNotifications as $notification) {
            $deleteIDs[] = $notification['id'];
        }
        $this->markAsSent($deleteIDs);

        foreach ($translatedNotifications as $notification) {
            $devices = $this->getDevices($notification, $this->systemType);
            foreach ($devices as $device) {
                $message = $notification['name'][ $device['user_language'] ];
                if ($message) {
                    $push = new Push();
                    $push->send(array($device), $message, $notification, $notification['id']);
                }
            }
        }
    }

    public function getTarget($info, $id = null): int
    {
        if ($id != null) {
            $sql    = 'SELECT * FROM appacman_push WHERE id = : id';
            $params = array(
                'id' => array('value' => $id, 'type' => PDO::PARAM_INT)
            );
            $info   = $this->mysql->query($sql, $params);
            if (count($info)) {
                $info = $info[0];
            }
        }

        $target  = 0;
        $devices = $this->getDevices($info, $this->systemType);
        foreach ($devices as $device) {
            $target += count(explode(',', $device['tokens']));
        }

        return $target;
    }

}