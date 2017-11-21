<?php

namespace Appacman\Model\Form;

class Select extends FormInput {

    /**
     * select simple (only one option)
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return '
            <select name="'.$this->fieldName.'" class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true">
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

        foreach($options as $option){
            $selected = in_array($option['id'], $values) !== false ? 'selected' : '';
            $optionsHTML .= '<option value="' . $option['id'] . '" '.$selected.'>' . $option['name'] . '</option>';
        }

        return $optionsHTML;
    }

    /**
     * from witch table has to load options?
     * @return array
     */
    protected function getOptions(){
        $lateralTable = str_replace('id_', '', $this->fieldName);
        return $this->loadOptions($lateralTable);
    }

    /**
     * load all possible options form lateral table
     * @param string $lateralTable
     * @return array
     */
    protected function loadOptions($lateralTable){
        $lateralTableLang = $lateralTable . '_lang';

        $params = array();
        $innerJoin = '';
        if( $this->mysql->tableExists($lateralTableLang) ){
            $innerJoin = 'INNER JOIN '.$lateralTableLang.' ON '.$lateralTableLang.'.id_'.$lateralTable.' = '.$lateralTable.'.id_'.$lateralTable.' AND '.$lateralTableLang.'.id_appacman_lang = :lang';
            $params['lang'] = array('value'=> $this->langID, 'type' => \PDO::PARAM_INT);
        }
        $sql = '
            SELECT '.$lateralTable.'.id_'.$lateralTable.' AS id, name
            FROM '.$lateralTable.'
            '.$innerJoin.'
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
        return array($this->getSeeValue($langID));
    }

}