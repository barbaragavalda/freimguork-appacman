<?php

namespace Appacman\Model;

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
                $this->name = strip_tags( $input->getValue() );
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
            }
            foreach($infoLang as $lang){
                $langID = $lang['id_appacman_lang'];
                foreach($lang as $field => $value){
                    if( !array_key_exists($field, $this->info) ){
                        $this->info[$field] = array();
                    }
                    $this->info[$field]['lang_'.$langID] = $value;
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

    public function save(){
        $post = array();
        $postLang = array();
        foreach($this->form as $input){
            if( $input->canSave() ){
                $value = array('value'=>$input->getSaveValue(), 'type'=>$input->getTypeValue());
                if( $input->onLangTable() ){
                    $postLang[ $input->getFieldName() ] = $value;
                }else{
                    $post[ $input->getFieldName() ] = $value;
                }
            }
        }
        r($post);
        r($postLang);
        r($_POST);
        exit;
    }

}