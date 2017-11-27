<?php

namespace Appacman\Model;

use Core\Model\Encryptor\TwoWay;
use Core\Model\File;
use Core\Model\Utils\StringUtils;
use Core\Utils\Exception;

class Item extends Page {

    private $form = array();

    public function __construct($id, $table){
        parent::__construct($id);
        $this->table = $table;
    }

    public function getName(){
        if( count($this->info) ){
            return StringUtils::truncateHtml($this->name, 35);
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

    /**
     * @param $table
     * @return mixed
     */
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
                $value = $input->getSaveValue();
                if( $value != null ){
                    if( $input->isOnLangTable() ){
                        $postLang = array_merge_recursive($postLang, $value);
                    }else{
                        $post = array_merge_recursive($post, $value);
                    }
                }
            }

            if( $this->id ){
                // update
                $this->update($post);
                foreach($postLang as $lang => $post){
                    $langID = str_replace('lang_', '', $lang);
                    $this->update($post, $langID);
                }
            }else{
                //insert
                $this->insert($post);
                foreach($postLang as $lang => $post){
                    $langID = str_replace('lang_', '', $lang);
                    $this->insert($post, $langID);
                }
            }
            $this->mysql->commit();
            return true;

        }catch (Exception $e){
            $this->mysql->rollBack();
            return false;
        }
    }

    /**
     * update item
     * @param $params
     * @param null $langID
     */
    private function update($params, $langID = null){
        $fields = $this->getFields($params);
        $tableName = $this->table;
        $whereLang = '';
        if( $langID != null ){
            $tableName = $this->table . '_lang';
            $whereLang = 'AND id_appacman_lang = :lang_id';
            $params['lang_id'] = array('value'=>$langID, 'type'=>\PDO::PARAM_INT);
        }

        $sql = '
            UPDATE '.$tableName.'
            SET '.$fields.'    
            WHERE id_'.$this->table.' = :id '.$whereLang.'
        ';
        $params['id'] = array('value'=>$this->id, 'type'=>\PDO::PARAM_INT);
        $this->mysql->query($sql, $params);
    }

    /**
     * create new item
     * @param $params
     * @param null $langID
     */
    private function insert($params, $langID = null){
        $fields = $this->getFields($params);
        $tableName = $this->table;
        $extraFields = '';
        if( $langID != null ){
            $tableName = $this->table . '_lang';
            $extraFields = ', id_'.$this->table.' = :id, id_appacman_lang = :lang_id';
            $params['id'] = array('value'=>$this->id, 'type'=>\PDO::PARAM_INT);
            $params['lang_id'] = array('value'=>$langID, 'type'=>\PDO::PARAM_INT);
        }

        $sql = '
            INSERT INTO '.$tableName.'
            SET '.$fields.$extraFields.'
        ';
        $this->mysql->query($sql, $params);

        if( $langID == null ){
            $this->id = $this->mysql->lastInsertId();
        }
    }

    /**
     * fields for update / insert query
     * @param $params
     * @return string
     */
    private function getFields($params){
        $set = array();
        foreach($params as $field => $param){
            $set[] = '`' . $field.'` = :'.$field;
        }
        return implode(', ', $set);
    }

    /**
     * delete item
     * @return bool
     */
    public function delete(){
        $this->deleteFiles();
        return $this->deleteFromDatabase();
    }

    /**
     * delete item files
     */
    private function deleteFiles(){
        $this->get();
        $files = array();
        foreach($this->form as $input){
            if( is_a($input, 'Appacman\Model\Form\GenericFile') ){
                $fileID = $input->getValue();
                if( is_array($fileID) ){
                    $files = array_merge($files, array_values($fileID));
                }else{
                    $files = array_merge($files, array($fileID));
                }
            }
        }

        $files = array_filter($files);
        foreach($files as $fileID){
            $file = new File($fileID);
            $file->deleteFromFileTable();
            $file->deleteFromDisk();
        }
    }

    /**
     * delete item on database
     * @return bool
     */
    private function deleteFromDatabase(){
        $success = false;
        $this->mysql->beginTransaction();

        // delete no language
        $sql = '
            DELETE FROM '.$this->table.'
            WHERE id_'.$this->table.' = :id
        ';
        $params = array(
            'id' => array('value'=> $this->id, 'type' => \PDO::PARAM_INT)
        );
        $this->mysql->query($sql, $params);

        // delete multi language
        if( $this->mysql->rowCount() == 1 ){
            $tableLang = $this->table . '_lang';
            if( $this->mysql->tableExists($tableLang) ){
                $sql = '
                    DELETE FROM '.$tableLang.'
                    WHERE id_'.$this->table.' = :id
                ';
                $this->mysql->query($sql, $params);
                if( $this->mysql->rowCount() >= 1 ){
                    $success = true;
                }
            }else{
                $success = true;
            }
        }

        if( $success ){
            $this->mysql->commit();
            return true;
        }else{
            $this->mysql->rollback();
            return false;
        }
    }

}