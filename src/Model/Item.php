<?php

namespace Appacman\Model;

use Core\Model\Encryptor\TwoWay;
use Core\Utils\Exception;

class Item extends Page {

    private $form = array();

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

    public function hasLang(){
        return $this->mysql->tableExists( $this->table.'_lang' );
    }

    /**
     * get the formulari for that item
     * @param array $languages
     * @return array
     */
    public function get($languages = array()){
        $this->initFields($this->table);
        $fields = $this->fields->get();

        foreach($fields as $field){
            $input = $this->getInputClass($field);
            $input->setLanguages($languages);
            $this->form[] = $input;

            // page title
            if( $field['show_on_breadcrumb'] && $this->name == '' ){
                $this->name = strip_tags( $input->getSeeValue() );
            }
            unset($field['show_on_breadcrumb']);
            unset($field['show_on_list']);
        }

        return $this->form;
    }

    /**
     * check if this item exists
     * @return bool
     */
    public function exists(){
        $info = $this->getInfo($this->table);

        if( count($info) ){
            $this->info = $info[0];

            // lang table
            $tableLang = $this->table . '_lang';
            if( $this->mysql->tableExists($tableLang) ){
                $infoLang = $this->getInfo($tableLang);
                foreach($infoLang as $lang){
                    $langID = $lang['id_appacman_lang'];
                    foreach($lang as $field => $value){
                        if( !array_key_exists($field, $this->info) ){
                            $this->info[$field] = array();
                        }
                        $this->info[$field]['lang_'.$langID] = $value;
                    }
                }
            }

            return true;
        }
        return false;
    }

    private function getInfo($table){
        $sql = '
            SELECT *, t.id_'.$table.' AS id
            FROM '.$table.' AS t
            WHERE t.id_'.$this->table.' = :id
        ';
        $params = array(
            'id' => array('value'=> $this->id, 'type' => \PDO::PARAM_INT)
        );
        return $this->mysql->query($sql, $params);
    }

    /**
     * saves item
     * @return bool
     */
    public function save(){
        $this->mysql->beginTransaction();

        try{
            // prepare post
            $post = array();
            $postLang = array();
            foreach($this->form as $input){
                if( $input->canSave() ){
                    $value = $input->getSaveValue();
                    if( $input->isOnLangTable() ){
                        $postLang = array_merge_recursive($postLang, $value);
                    }else{
                        $post = array_merge_recursive($post, $value);
                    }
                }
            }

            // update
            $this->update($post);
            foreach($postLang as $lang => $post){
                $langID = str_replace('lang_', '', $lang);
                $this->update($post, $langID);
            }

            $this->mysql->commit();
            return true;

        }catch (Exception $e){
            $this->mysql->rollBack();
            return false;
        }
    }

    private function update($params, $langID = null){
        $set = array();
        foreach($params as $field => $param){
            $set[] = '`' . $field.'` = :'.$field;
        }

        $tableName = $this->table;
        $whereLang = '';
        if( $langID != null ){
            $tableName = $this->table . '_lang';
            $whereLang = 'AND id_'.$tableName.' = :lang_id';
            $params['lang_id'] = array('value'=>$langID, 'type'=>\PDO::PARAM_INT);
        }

        $sql = '
            UPDATE '.$tableName.'
            SET '.implode(', ', $set).'    
            WHERE id_'.$this->table.' = :id '.$whereLang.'
        ';
        $params['id'] = array('value'=>$this->id, 'type'=>\PDO::PARAM_INT);
        $this->mysql->query($sql, $params);
    }

}