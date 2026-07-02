<?php

namespace Appacman\Model\Push;

use Core\Model\Model;
use PDO;

class Statistic extends Model
{

    private bool $hasStatistics;

    public function __construct($id)
    {
        parent::__construct();

        $this->id            = $id;
        $this->hasStatistics = $this->mysql->tableExists('appacman_push_statistic');
    }

    public function update(int $devices): void
    {
        if ($this->hasStatistics) {
            $sql    = '
                INSERT INTO appacman_push_statistic
                SET id_appacman_push = :id,
                    devices = :devices
            ';
            $params = array(
                'id'      => array('value' => $this->id, 'type' => PDO::PARAM_INT),
                'devices' => array('value' => $devices, 'type' => PDO::PARAM_INT),
            );
            if ($this->exists()) {
                $sql = '
                    UPDATE appacman_push_statistic
                    SET devices = devices + :devices
                    WHERE id_appacman_push = :id
                ';
            }

            $this->mysql->query($sql, $params);
        }
    }

    public function click(): void
    {
        if ($this->hasStatistics) {
            $sql    = '
                UPDATE appacman_push_statistic
                SET clicks = clicks + 1
                WHERE id_appacman_push = :id
            ';
            $params = array(
                'id' => array('value' => $this->id, 'type' => PDO::PARAM_INT),
            );
            $this->mysql->query($sql, $params);
        }
    }

    private function exists(): bool|array
    {
        if ($this->hasStatistics) {
            $sql       = '
                SELECT *
                FROM appacman_push_statistic
                WHERE id_appacman_push = :id
            ';
            $params    = array(
                'id' => array('value' => $this->id, 'type' => PDO::PARAM_INT)
            );
            $statistic = $this->mysql->query($sql, $params);

            if (count($statistic)) {
                return $statistic[0];
            }
        }
        return false;
    }

    public function get(): array
    {
        if ($this->hasStatistics) {
            $sql       = '
                SELECT devices, clicks
                FROM appacman_push_statistic
                WHERE id_appacman_push = :id
            ';
            $params    = array(
                'id' => array('value' => $this->id, 'type' => PDO::PARAM_INT)
            );
            $statistic = $this->mysql->query($sql, $params);

            if (count($statistic)) {
                return $statistic[0];
            }

            return array(
                'devices' => '0',
                'clicks'  => '0'
            );
        }
        return array();
    }

}