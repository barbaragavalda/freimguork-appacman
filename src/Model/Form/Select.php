<?php

namespace Appacman\Model\Form;

use Appacman\Model\ExtraUser;
use Core\Model\Encryptor\TwoWay;
use Core\Utils\Session;

class Select extends FormInput {

    public function getSeeValue($langID = null){
        if( $this->value ){
            $options = $this->getOptions();
            foreach($options as $option){
                if( $option['id'] == $this->value ){
                    return $option['name'];
                }
            }
        }
        return '-';
    }

    /**
     * select simple (only one option)
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return '
            <select name="'.$this->getInputName($langID).'" class="deepLink form-control select2 select2-hidden-accessible" data-placeholder="'.gettext('Selecciona').' '.$this->getPlaceholder().'" style="width: 100%;" tabindex="-1" aria-hidden="true">
                ' . $this->getOptionsHTML($langID) . '
            </select>
        ';
    }

    /**
     *
     * @param $langID
     * @return string
     */
    protected function getOptionsHTML($langID){
        $optionsHTML = '';
        $options = $this->getOptions();
        $values = $this->loadValues($langID);

        $optionsHTML .= '<option></option>';
        foreach($options as $option){
            $selected = in_array($option['id'], $values) !== false ? 'selected' : '';
            $name = $option['name'];
            if( array_key_exists('created', $option) ){
                $hash = $option['id'] . '_' . $option['created'] . '_name';
                $name = TwoWay::decrypt($option['name'], $hash);
            }
            $optionsHTML .= '<option value="' . $option['id'] . '" '.$selected.'>' . $name . '</option>';
        }

        return $optionsHTML;
    }

    /**
     * from witch table has to load options?
     * @param $table
     * @param string $extraFields
     * @return array
     */
    protected function getOptions($table = null, $extraFields = ''){
        $lateralTable = $table;
        if( $lateralTable == null ) $lateralTable = str_replace('id_', '', $this->fieldName);
        return $this->loadOptions($lateralTable, $extraFields);
    }

    /**
     * load all possible options form lateral table
     * @param string $lateralTable
     * @param string $extraFields
     * @return array
     */
    protected function loadOptions($lateralTable, $extraFields = ''){
        $lateralTableLang = $lateralTable . '_lang';

        $params = array();
        $where = '';
        $session = Session::getInstance();
        $profile = $session->get('profile_info');
        if( $profile['profile'] == ExtraUser::OWNER ) {
            $table = '';
            if ($this->mysql->fieldExists($lateralTable, $profile['field'])) {
                $table = $lateralTable;
            } else if ($this->mysql->fieldExists($lateralTableLang, $profile['field'])) {
                $table = $lateralTableLang;
            }
            if( $table ){
                $where = 'WHERE ' . $table . '.' . $profile['field'] . ' = :id';
                $params['id'] = array('value' => $profile['value'], 'type' => \PDO::PARAM_INT);
            }
        }

        $innerJoin = '';
        if( $this->mysql->tableExists($lateralTableLang) ){
            $innerJoin = 'INNER JOIN '.$lateralTableLang.' ON '.$lateralTableLang.'.id_'.$lateralTable.' = '.$lateralTable.'.id_'.$lateralTable.' AND '.$lateralTableLang.'.id_appacman_lang = :lang';
            $params['lang'] = array('value'=> $this->langID, 'type' => \PDO::PARAM_INT);
        }
        $sql = '
            SELECT '.$lateralTable.'.id_'.$lateralTable.' AS id, name ' . $extraFields . '
            FROM '.$lateralTable.'
            '.$innerJoin.'
            '.$where.'
            ORDER BY name ASC
        ';
        return $this->mysql->query($sql, $params);
    }

    /**
     * get selected options
     * @param $langID
     * @return array
     */
    protected function loadValues($langID){
        $values = array($this->value);
        $postValue = $this->getPostValue($langID);
        if( $postValue ){
            $values = array($postValue);
        }
        return $values;
    }

    /**
     * Check if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        $postValue = $this->getPostValue($langID);
        if( $postValue == null && $this->isRequired ){
            return gettext('Campo obligatorio.');
        }
        return false;
    }

    public function save($itemID, $langID = null){
        return false;
    }

}