<?php

namespace Appacman\Model;

use Core\Model\File;
use Core\Model\Model;

class Business extends Model {

    /**
     * @var array $config. Business info
     */
    public $info = array();

    public function __construct(){
        parent::__construct();

        $sql = '
            SELECT `name`, `value`
            FROM appacman_config
        ';
        $config = $this->mysql->query($sql);

        if( count($config) ){
            foreach($config as $info){
                $name = $info['name'];
                $value = $info['value'];

                if( $name == 'logo' ){
                    $file = new File( $value );
                    $value = $file->getAbsolutePath();
                }

                $this->info[$name] = $value;
            }
        }
    }

    /**
     * load user info from session
     */
    public function getInfo(){
        return $this->info;
    }

}