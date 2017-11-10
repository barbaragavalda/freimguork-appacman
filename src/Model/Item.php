<?php

namespace Appacman\Model;

class Item extends Page {

    public function __construct($id, $table){
        parent::__construct($id);
        $this->table = $table;
    }

    public function getName(){
        if( count($this->info) ){
            return $this->name;
        }
        return gettext('Crear nuevo item');
    }

    /**
     * get the formulari for that item
     * @return array
     */
    public function get(){
        $this->initFields($this->table);
        $fields = $this->fields->get();

        $form = array();
        foreach($fields as $field){
            $input = $this->getInputClass($field);
            $form[] = $input;

            // page title
            if( $field['show_on_breadcrumb'] && $this->name == '' ){
                $this->name = strip_tags( $input->getValue() );
            }
            unset($field['show_on_breadcrumb']);
            unset($field['show_on_list']);
        }

        return $form;
    }

    /**
     * check if this item exists
     * @return bool
     */
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
            SELECT *, t.id_'.$this->table.' AS id
            FROM '.$this->table.' AS t
            '.$innerJoin.'
            WHERE t.id_'.$this->table.' = :id
        ';
        $info = $this->mysql->query($sql, $params);

        if( count($info) ){
            $this->info = $info[0];
            return true;
        }
        return false;
    }

}