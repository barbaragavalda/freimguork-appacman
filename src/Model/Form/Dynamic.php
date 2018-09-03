<?php

namespace Appacman\Model\Form;

use Appacman\Model\Item;

class Dynamic extends FormInput {

    private $forms = array();

    public function __construct($info, $id, $table = null){
        parent::__construct($info, $id, $table);

        $sql = '
            SELECT *
            FROM '.$this->fieldName.'
            WHERE id_'.$table.' = :id
        ';
        $params = array(
            'id' => array('value' => $id, 'type' => \PDO::PARAM_INT)
        );
        $items = $this->mysql->query($sql, $params);
        if( count($items) ){
            foreach($items as $item){
                $item = new Item($item['id_'.$this->fieldName], $this->fieldName);
                $item->exists();
                $this->forms[] = $item;
            }
        }else{
            $this->forms[] = new Item(false, $this->fieldName);
        }
    }

    /**
     * Field name (description useful for the user)
     * @return string
     */
    public function getName(){
        return $this->name . '
            <a href="#" data-field="' . $this->fieldName. '" data-id="' . $this->id. '" data-table="' . $this->table. '" class="add-dynamic-field btn btn-success btn-xs" title="' . gettext('Añadir') . '"><i class="fa fa-plus"></i></a>
        ';
    }

    /**
     * remove tags on list
     * @param null $langID
     * @return string
     */
    public function getListValue($langID = null){
        return '';
    }

    /**
     * input type text
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $html = '';
        foreach($this->forms as $form){
            $html .= $this->getItemHTML($form);
        }

        return '
            <div id="content-' . $this->fieldName. '" class="box-body">' . $html . '</div>
        ';
    }

    public function getItemHTML($form = null){
        if( $form == null ){
            $form = new Item(false, $this->fieldName);
            $this->forms[] = $form;
        }

        $html = '
            <div class="with-border">
                <div class="box-body">
        ';
        $inputs = $form->get($this->languages);
        foreach($inputs as $input){
            $input->isMultiple();
            if( $input->isVisible() ){
                $required = '';
                if( $input->isRequired() ) $required = ' required';
                $html .= '
                    <div class="clearfix">
                        <label class="col-sm-2 control-label' . $required . '">' . $input->getName() . '</label>
                        <div class="col-sm-10">
                            ' . $input->getFormHTML() . '
                        </div>
                    </div>
                ';
            }else{
                if( $input->getFieldName() == 'id_' . $this->table ){
                    $input->setValue($this->id);
                }
                $html .= $input->getHTML();
            }
        }
        $html .= '
                    <a href="#" data-id="' . $form->getID() . '" data-field="' . $this->fieldName. '" data-table="' . $this->table. '" class="delete-dynamic-field pull-right btn btn-danger btn-xs" title="' . gettext('Eliminar') . '" data-toggle="confirmation"><i class="fa fa-trash"></i></a>
                </div>
            </div>
        ';

        return $html;
    }

    /**
     * Check if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        return false;
    }

    public function canSave($langID = null){
        return false;
    }

    /**
     * @param int $itemID
     * @param null $langID
     * @return bool     error
     */
    public function save($itemID, $langID = null){
        $this->id = $itemID;

        if( $this->delete() ){
            $loopInputs = $this->forms[0]->get($this->languages);

            // num forms
            $lang = null;
            $firstInput = $loopInputs[1];
            if( $firstInput->isOnLangTable() ){
                $lang = $this->languages[0]['id'];
            }
            $numForms = count( $_POST[$firstInput->getInputName($lang)] );

            for($i=0; $i<$numForms; $i++){
                $form = new Item(false, $this->fieldName);
                $inputs = $form->get($this->languages);

                $empty = true;
                foreach($inputs as &$input){
                    $input->isMultiple($i);
                    if( is_a($input, 'Appacman\Model\Form\GenericFile') ){
                        $input->initFile();
                    }
                    if( $input->getFieldName() == 'id_' . $this->table ){
                        $_POST[$input->getInputName(null, false)][$i] = $this->id;
                    }else{
                        // only save it if some field is not empty
                        $value = $input->getSaveValue();
                        if( $value ){
                            if( $input->isOnLangTable() ){
                                foreach($this->languages as $language){
                                    $key = array_keys($value['lang_'.$language['id']])[0];
                                    $value = $value['lang_'.$language['id']][$key]['value'];
                                    if( $value ) $empty = false;
                                }
                            }else{
                                $key = array_keys($value)[0];
                                $value = $value[$key]['value'];
                                if( $value ) $empty = false;
                            }
                        }
                    }
                }
                if( !$empty ){
                    $form->setForm($inputs);
                    $form->preparePost();
                    $success = $form->save();
                    if( !$success ){
                        return true;
                    }
                }
            }
            return false;
        }
        return true;
    }

    /**
     * remove current rows on database
     * @return bool     success
     */
    private function delete(){
        $sql = '
            SELECT id_' . $this->fieldName . ' AS id
            FROM ' . $this->fieldName . '
            WHERE id_' . $this->table . ' = :id
        ';
        $params = array(
            'id' => array('value' => $this->id, 'type' => \PDO::PARAM_INT)
        );
        $items = $this->mysql->query($sql, $params);

        if( count($items) ){
            return $this->deleteWithID($items);
        }
        return true;
    }

    /**
     * remove current rows on database
     * @param array $ids    ids to delete
     * @return bool         success
     */
    public function deleteWithID($ids){
        $ids = implode(',', array_column($ids, 'id'));
        $sql = '
            DELETE FROM ' . $this->fieldName . '
            WHERE id_' . $this->fieldName . ' IN(' . $ids . ')
        ';
        $this->mysql->query($sql);
        if( $this->mysql->getState() && $this->mysql->tableExists($this->fieldName . '_lang') ){
            $sql = '
                DELETE FROM ' . $this->fieldName . '_lang
                WHERE id_' . $this->fieldName . ' IN(' . $ids . ')
            ';
            $this->mysql->query($sql);
        }
        return $this->mysql->getState();
    }

}