<?php

namespace Appacman\Model\Push;

use Core\Model\Model;
use Core\Model\Push\Push;

class Statistic extends Model {

    private $hasStatistics = false;

    public function __construct($id){
        parent::__construct();

        $this->id = $id;
        $this->hasStatistics = $this->mysql->tableExists('appacman_push_statistic');
    }

    public function update($counter = 1, $devices = null){
        if( $this->hasStatistics ){
            $fields = '';
            $params = array(
                'id' => array('value' => $this->id, 'type' => \PDO::PARAM_INT)
            );
            if( $devices != null ){
                $fields = ', devices = devices + :devices';
                $params['devices'] = array('value' => $devices, 'type' => \PDO::PARAM_INT);
            }

            $sql = '
                INSERT INTO appacman_push_statistic
                SET id_appacman_push = :id,
                    counter = :counter
                    ' . $fields . '
            ';
            if( $this->exists() ){
                $sql = '
                    UPDATE appacman_push_statistic
                    SET counter = counter + :counter' . $fields . '
                    WHERE id_appacman_push = :id
                ';
            }
            $params['counter'] = array('value' => $counter, 'type' => \PDO::PARAM_INT);

            $this->mysql->query($sql, $params);
        }
    }

    /**
     * @return bool|array     exists?
     */
    private function exists(){
        if( $this->hasStatistics ) {
            $sql = '
            SELECT *
            FROM appacman_push_statistic
            WHERE id_appacman_push = :id
        ';
            $params = array(
                'id' => array('value' => $this->id, 'type' => \PDO::PARAM_INT)
            );
            $statistic = $this->mysql->query($sql, $params);

            if (count($statistic)) {
                return $statistic[0];
            }
        }
        return false;
    }

    public function get(){
        if( $this->hasStatistics ) {
            $sql = '
            SELECT devices, counter AS clicks, counter_scan AS conversion
            FROM appacman_push_statistic
            WHERE id_appacman_push = :id
        ';
            $params = array(
                'id' => array('value' => $this->id, 'type' => \PDO::PARAM_INT)
            );
            $statistic = $this->mysql->query($sql, $params);

            if (count($statistic)) {
                return $statistic[0];
            }

            return array(
                'devices' => '0',
                'clicks' => '0',
                'conversion' => '0',
            );
        }
        return array();
    }
    
}