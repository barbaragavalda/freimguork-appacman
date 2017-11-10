<?php
namespace Appacman\Model;


use Core\Model\Model;

class Menu extends Model {

    private $blocks = array();

    public function __construct(){
        parent::__construct();

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
            WHERE ac.id_appacman_content > 1
            ORDER BY ac.order ASC
        ';
        $params = array(
            'lang' => array('value'=>$this->langID, 'type'=>\PDO::PARAM_INT)
        );
        $contents = $this->mysql->query($sql, $params);

        $aside = $this->blocks;
        if( count($contents) ){
            $user = User::getInstance();
            foreach($contents as $content){
                $permissions = $user->getContentPermissions($content['id_appacman_content']);
                if( count($permissions) ){
                    $content['counter'] = $this->getCounter($content['table_name']);
                    $content['permissions'] = $permissions;
                    $aside['b'.$content['id_appacman_block']]['list'][] = $content;
                }
            }
        }

        return $aside;
    }

    private function getCounter($tableName){
        $sql = '
            SELECT COUNT(*) AS counter
            FROM '.$tableName.' AS t
        ';
        $counter = $this->mysql->query($sql);

        if( count($counter) ){
            return $counter[0]['counter'];
        }
        return 0;
    }

}