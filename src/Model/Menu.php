<?php
namespace Appacman\Model;


use Appacman\Model\Utils\Permissions;
use Core\Model\Model;
use Core\Utils\Session;

class Menu extends Model {

    private $profileInfo = null;

    private $blocks = array();

    public function __construct($profileInfo){
        parent::__construct();
        $this->profileInfo = $profileInfo;

        $sql = '
            SELECT ab.id_appacman_block, abl.name
            FROM appacman_block AS ab 
            INNER JOIN appacman_block_lang AS abl ON ab.id_appacman_block = abl.id_appacman_block AND abl.id_appacman_lang = :lang
            ORDER BY ab.order ASC
        ';
        $params = array(
            'lang' => array('value'=>$this->langID, 'type'=>\PDO::PARAM_INT)
        );
        $blocks = $this->mysql->query($sql, $params);

        if( count($blocks) ){
            foreach($blocks as $block){
                $this->blocks['b'.$block['id_appacman_block']] = array('name' => $block['name'], 'list' => array());
            }
        }else{
            $this->blocks['b1'] = array('name' => null, 'list' => array());
        }
    }

    public function get(){
        $sql = '
            SELECT ac.id_appacman_content, ac.icon, ac.id_appacman_block, ac.table_name, acl.name
            FROM appacman_content AS ac
            INNER JOIN appacman_content_lang AS acl ON ac.id_appacman_content = acl.id_appacman_content AND acl.id_appacman_lang = :lang
            ORDER BY ac.order ASC
        ';
        $params = array(
            'lang' => array('value'=>$this->langID, 'type'=>\PDO::PARAM_INT)
        );
        $contents = $this->mysql->query($sql, $params);

        $aside = $this->blocks;
        $user = User::getInstance();
        if( count($contents) ){
            foreach($contents as $content){
                $permissions = $user->getContentPermissions($content['id_appacman_content']);
                if( count($permissions) ){
                    $isOwn = false;
                    foreach($permissions as $permission){
                        if( $permission['code'] == Permissions::OWN ){
                            $isOwn = true;
                            break;
                        }
                    }
                    $content['counter'] = $this->getCounter($content['table_name'], $isOwn);
                    $content['permissions'] = $permissions;
                    $aside['b'.$content['id_appacman_block']]['list'][] = $content;
                }
            }
        }

        $firebasePermission = $user->hasPermission(0, Permissions::FIREBASE);
        if( $user->hasPermission(0, Permissions::FIREBASE) ){
            $link = array(
                'id_appacman_content' => null,
                'icon' => 'fa-bar-chart',
                'id_appacman_block' => 1,
                'table_name' => null,
                'name' => $firebasePermission['name'],
                'counter' => 0,
                'permissions' => array(),
                'link' => $this->getConfig('firebase'),
            );
            if( array_key_exists('b1', $aside) ){
                $aside['b1']['list'][] = $link;
            }else{
                $aside['b1']['list'] = array( $link );
            }
        }

        return $aside;
    }

    private function getCounter($tableName, $isOwn = false){
        $sql = '
            SELECT COUNT(*) AS counter
            FROM '.$tableName.' AS t
        ';
        if( $isOwn && $this->profileInfo != null ){
            $sql .= 'WHERE t.' . $this->profileInfo['field'] . ' = ' . $this->profileInfo['value'];
        }
        $counter = $this->mysql->query($sql);

        if( count($counter) ){
            return $counter[0]['counter'];
        }
        return 0;
    }

    private function getConfig($name){
        $sql = '
            SELECT ac.value AS value
            FROM appacman_config AS ac
            WHERE ac.name = :name
        ';
        $params = array(
            'name' => array('value' => $name, 'type' => \PDO::PARAM_STR)
        );
        $config = $this->mysql->query($sql, $params);

        if( count($config) ) return $config[0]['value'];
        return false;
    }

}