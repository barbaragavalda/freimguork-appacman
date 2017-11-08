<?php

namespace Appacman\Model;


use Appacman\Model\Utils\Field;
use Core\Model\Model;

class Content extends Model {

    /**
     * @var int $id. Content id
     */
    private $id = 0;

    /**
     * @var array $info. Info of the content
     */
    private $info = array();

    /**
     * @var \Appacman\Model\Utils\Field $fields. Fields info
     */
    private $fields = array();

    public function __construct($id){
        parent::__construct();

        $this->id = $id;
    }

    public function get(){
        $fields = array_column($this->fields->getFieldsForList(), 'field_name');
        $extraFields = count($fields) ? ', '.implode(', ', $fields) : '';

        $table = $this->info['table_name'];
        $tableLang = $table . '_lang';
        $params = array();
        $sql = '
            SELECT t.id_'.$table.' AS id '.$extraFields.'
            FROM '.$table.' AS t
        ';
        if( $this->mysql->tableExists($tableLang) ){
            $sql .= ' INNER JOIN '.$tableLang.' AS tl ON tl.id_'.$table.' = t.id_'.$table.' AND tl.id_appacman_lang = :lang';
            $params['lang'] = array('value'=> $this->langID, 'type' => \PDO::PARAM_INT);
        }

        return $this->mysql->query($sql, $params);
    }

    public function getID(){
        return $this->id;
    }

    public function getName(){
        return $this->info['name'];
    }

    public function getTable(){
        return $this->info['table_name'];
    }

    public function getOrderBy(){
        // order by on data base
        $orders = explode(', ', $this->info['order_by']);
        $orderBy = array();
        foreach($orders as $order){
            $array = explode(' ', $order);
            $orderBy[ $array[0] ] = $array[1];
        }

        // setup order for javascript
        $order = array();
        $fields = $this->fields->get();
        for($i=0; $i<count($fields)-1; $i++){
            $field = $fields[$i]['field_name'];
            if( array_key_exists($field, $orderBy) ){
                $orderType = strtolower( $orderBy[$field] );
                $order[] = array($i, $orderType);
            }
        }

        return $order;
    }

    public function exists(){
        $sql = '
            SELECT ac.table_name, ac.id_appacman_list_type, ac.order_by, acl.name
            FROM appacman_content AS ac
            INNER JOIN appacman_content_lang AS acl ON acl.id_appacman_content = ac.id_appacman_content AND acl.id_appacman_lang = :lang
            WHERE ac.id_appacman_content = :id
        ';
        $params = array(
            'id'    => array('value' => $this->id,      'type' => \PDO::PARAM_INT),
            'lang'  => array('value' => $this->langID,  'type' => \PDO::PARAM_INT)
        );
        $content = $this->mysql->query($sql, $params);

        if( count($content) ){
            $this->info = $content[0];
            $this->fields = new Field($this->info['table_name'], $this->id);
            return true;
        }
        return false;
    }

    public function getHeaders(){
        return $this->fields->getFieldsForList();
    }

}

if(!function_exists("array_column")) {
    function array_column($array,$column_name){
        return array_map(function($element) use($column_name){return $element[$column_name];}, $array);
    }
}
