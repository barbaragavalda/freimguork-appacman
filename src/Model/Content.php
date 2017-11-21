<?php

namespace Appacman\Model;

class Content extends Page {

    public function getName(){
        return $this->info['name'];
    }

    public function getTable(){
        return $this->info['table_name'];
    }

    public function getTableHeaders(){
        return $this->fields->getFieldsForList();
    }

    public function getListType(){
        return $this->info['list_type'];
    }

    public function getOrderBy(){
        // order by on data base
        $orders = explode(', ', $this->info['order_by']);
        $orderBy = array();
        foreach($orders as $order){
            $array = explode(' ', $order);
            if( count($array) == 2 ){
                $orderBy[ $array[0] ] = $array[1];
            }else{
                $orderBy[ $array[0] ] = 'asc';
            }
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

    /**
     * get the list of items for this content
     * @return array
     */
    public function get(){
        $fields = $this->fields->getFieldsForList();
        $fieldsNames = array_column($fields, 'field_name');
        $extraFields = count($fieldsNames) ? ', '.implode(', ', $fieldsNames) : '';

        // table rows
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
        $rows = $this->mysql->query($sql, $params);

        // prepare rows for list
        foreach($rows as &$row){
            foreach($fields as $field){
                $input = $this->getInputClass($field, $row);
                $row[ $input->getFieldName() ] = $input->getListValue();
            }
        }
        return $rows;
    }

    /**
     * check if this content exists
     * @return bool
     */
    public function exists(){
        $sql = '
            SELECT ac.table_name, alt.name AS list_type, ac.order_by, acl.name
            FROM appacman_content AS ac
            INNER JOIN appacman_content_lang AS acl ON acl.id_appacman_content = ac.id_appacman_content AND acl.id_appacman_lang = :lang
            LEFT JOIN appacman_list_type AS alt ON alt.id_appacman_list_type = ac.id_appacman_list_type
            WHERE ac.id_appacman_content = :id
        ';
        $params = array(
            'id'    => array('value' => $this->id,      'type' => \PDO::PARAM_INT),
            'lang'  => array('value' => $this->langID,  'type' => \PDO::PARAM_INT)
        );
        $content = $this->mysql->query($sql, $params);

        if( count($content) ){
            $this->info = $content[0];
            $this->table = $this->getTable();
            $this->initFields($this->info['table_name'], $this->id);
            return true;
        }
        return false;
    }

}