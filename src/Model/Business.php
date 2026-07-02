<?php

namespace Appacman\Model;

use Core\Model\File;
use Core\Model\Model;

class Business extends Model
{

    public array $info = array();

    public function __construct()
    {
        parent::__construct();

        $sql    = '
            SELECT `name`, `value`
            FROM appacman_config
        ';
        $config = $this->mysql->query($sql);

        if (count($config)) {
            foreach ($config as $info) {
                $name  = $info['name'];
                $value = $info['value'];

                if ($name == 'logo') {
                    $file  = new File($value);
                    $value = $file->getAbsolutePath();
                }

                $this->info[ $name ] = $value;
            }
        }
    }

    public function getInfo(): array
    {
        return $this->info;
    }

}