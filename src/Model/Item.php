<?php

namespace Appacman\Model;


use Appacman\Model\Utils\Field;
use Core\Model\Model;

class Item extends Model {

    /**
     * @var int $id. Item id
     */
    private $id = 0;

    /**
     * @var string $table. Table name
     */
    private $table = '';

    /**
     * @var array $info. Info of the content
     */
    private $info = array();

    /**
     * @var \Appacman\Model\Utils\Field $fields. Fields info
     */
    private $fields = array();

    public function __construct($id, $table){
        parent::__construct();

        $this->id = $id;
        $this->table = $table;
    }

    public function get(){
        $fields = $this->fields->get();

        $form = array();
        foreach($fields as $field){
            $field['value'] = $this->info[ $field['field_name'] ];
            unset($field['show_on_list']);
            $form[] = $field;
        }

        return $form;
    }

    public function getID(){
        return $this->id;
    }

    public function getName(){
        return 'test';
        //return $this->info['name'];
    }

    public function exists(){
        $tableLang = $this->table . '_lang';
        $params = array(
            'id' => array('value'=> $this->id, 'type' => \PDO::PARAM_INT)
        );

        // lang table
        $innerJoin = '';
        if( $this->mysql->tableExists($tableLang) ){
            $innerJoin = 'INNER JOIN '.$tableLang.' AS tl ON tl.id_'.$this->table.' = t.id_'.$this->table.' AND tl.id_appacman_lang = :lang';
            $params['lang'] = array('value'=> $this->langID, 'type' => \PDO::PARAM_INT);
        }

        $sql = '
            SELECT *
            FROM '.$this->table.' AS t
            '.$innerJoin.'
            WHERE t.id_'.$this->table.' = :id
        ';
        $info = $this->mysql->query($sql, $params);

        if( count($info) ){
            $this->info = $info[0];
            $this->fields = new Field($this->table);
            return true;
        }
        return false;
    }

}