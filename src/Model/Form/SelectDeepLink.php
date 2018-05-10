<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\StringUtils;

class SelectDeepLink extends Select {

    /**
     * select simple (only one option)
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $options = $this->getOptionsHTML($langID);

        $selects = '
            <select name="'.$this->fieldName.'" id="deeplink" class="form-control select2 select2-hidden-accessible" data-placeholder="'.gettext('Selecciona').' '.$this->getPlaceholder().'" style="width: 100%;" tabindex="-1" aria-hidden="true">
                ' . $options['main'] . '
            </select>
        ';

        foreach($options['secondary'] as $select){
            $selects .= $select;
        }
        return $selects;
    }

    /**
     *
     * @param $langID
     * @return array
     */
    protected function getOptionsHTML($langID){
        $mainHTML = '';
        $secondaryOptionsHTML = array();
        $options = $this->getOptions('appacman_push_deeplink', ', table_name, format');


        $mainHTML .= '<option value=""></option>';
        foreach($options as $mainOption){
            $format = str_replace('{id}', '', $mainOption['format']);
            $selectedMain= ( StringUtils::startsWidth($this->value, $format) ) ? 'selected' : '';
            $mainHTML .= '<option value="' . $mainOption['id'] . '_' . $mainOption['format'] . '" '.$selectedMain.' data-id="' . $mainOption['id'] . '" >' . $mainOption['name'] . '</option>';

            if( $mainOption['table_name'] ){
                $secondaryOptions = $this->getOptions($mainOption['table_name']);
                if( count($secondaryOptions) ){
                    $secondaryHTML = '<select name="'.$this->fieldName.'_' . $mainOption['id'] . '" class="deepLinkID form-control select2 select2-hidden-accessible" data-placeholder="'.gettext('Selecciona').' '.$this->getPlaceholder().'" style="width: 100%;" tabindex="-1" aria-hidden="true">';
                    $value = str_replace($format, '', $this->value);
                    foreach($secondaryOptions as $secondaryOption){
                        $selectedSecondary = $selectedMain && $value == $secondaryOption['id'] ? 'selected' : '';
                        $secondaryHTML .= '<option value="' . $secondaryOption['id'] . '" '.$selectedSecondary.'>' . $secondaryOption['name'] . '</option>';
                    }
                    $secondaryHTML .= '</select>';
                    $secondaryOptionsHTML[$mainOption['table_name']] = $secondaryHTML;
                }
            }
        }

        return array(
            'main' => $mainHTML,
            'secondary' => $secondaryOptionsHTML,
        );
    }

    /**
     * post value
     * @param int|null $langID
     * @return string
     */
    protected function getPostValue($langID = null){
        $inputName = $this->getInputName($langID);
        if( isset($_POST[$inputName]) ){
            $mainSelect = explode('_', $_POST[$inputName], 2);
            if( isset($_POST[ $inputName . '_' . $mainSelect[0] ]) ){
                return str_replace('{id}', $_POST[ $inputName . '_' . $mainSelect[0] ], $mainSelect[1]);
            }
            return $mainSelect[1];
        }
        return '';
    }

}