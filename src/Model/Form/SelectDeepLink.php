<?php

namespace Appacman\Model\Form;

use Appacman\Model\ExtraUser;
use Appacman\Model\User;
use Core\Model\Utils\DateUtils;
use Core\Model\Utils\StringUtils;
use Core\Utils\Session;

class SelectDeepLink extends Select {

    private $profileID = null;

    protected function getOptions($table = null, $extraFields = ''){
        return parent::getOptions('appacman_push_deeplink', $extraFields);
    }

    public function getSeeValue($langID = null){
        return $this->value;
    }

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

        // remove basic deep links
        $session = Session::getInstance();
        $profile = $session->get('profile_info');
        if( class_exists('Appacman\\Model\\ExtraUser') ) {
            if ($profile['profile'] == ExtraUser::OWNER) {
                $newOptions = array();
                foreach ($options as $option) {
                    if ($option['table_name'] != '') $newOptions[] = $option;
                }
                $options = $newOptions;
            }
        }

        $mainHTML .= '<option value=""></option>';
        foreach($options as $mainOption){
            $format = str_replace('{id}', '', $mainOption['format']);

            $selectedMain = ( StringUtils::startsWidth($this->value, $format) ) ? 'selected' : '';
            $mainHTML .= '<option value="' . $mainOption['id'] . '_' . $mainOption['format'] . '" '.$selectedMain.' data-id="' . $mainOption['id'] . '" >' . $mainOption['name'] . '</option>';

            if( $mainOption['table_name'] ){
                $secondaryHTML = '<div style="margin-top: 10px">' . $this->getSelectOptions($mainOption['table_name'], $selectedMain, $mainOption['id']) . '</div>';
                $secondaryOptionsHTML[] = $secondaryHTML;
            }
        }

        return array(
            'main' => $mainHTML,
            'secondary' => $secondaryOptionsHTML,
        );
    }

    private function getSelectOptions($tableName, $selectedMain, $mainOptionID){
        $secondaryOptions = $this->getOptions($tableName);

        if( $secondaryOptions !== null ){
            $secondaryHTML = '<select name="'.$this->fieldName.'_' . $mainOptionID . '" class="deepLinkID form-control select2 select2-hidden-accessible" data-placeholder="'.gettext('Selecciona').' '.$this->getPlaceholder().'" style="width: 100%;" tabindex="-1" aria-hidden="true">';
            if( count($secondaryOptions) ){
                $id = explode('_', $this->value);
                $value = '';
                if( isset($_POST[$this->fieldName . '_' .$id[0]]) ){
                    $value = $_POST[$this->fieldName . '_' .$id[0]];
                }else if( $this->value ){
                    $value = explode('=', $this->value);
                    $value = $value[1];
                }
                foreach($secondaryOptions as $secondaryOption){
                    $selectedSecondary = $selectedMain && $value == $secondaryOption['id'] ? 'selected' : '';
                    $secondaryHTML .= '<option value="' . $secondaryOption['id'] . '" '.$selectedSecondary.'>' . $secondaryOption['name'] . '</option>';
                }
            }else{
                $secondaryHTML .= '<option disabled selected>-</option>';
            }
            $secondaryHTML .= '</select>';
            return $secondaryHTML;
        }

        return '';
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
            if( count($mainSelect) > 1 ) return $mainSelect[1];
        }
        return '';
    }

}