<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;

class SelectEncryptedTwoWay extends Select {

    public function getSeeValue($langID = null){
        if( $this->getPostValue($langID) ) $this->value = $this->getPostValue($langID);
        if( $this->value ){
            $options = $this->getOptions();
            foreach($options as $option){
                if( $option['id'] == $this->value ){
                    $hash = $option['id'] . '_' . $option['created'] . '_name';
                    return TwoWay::decrypt($option['name'], $hash);
                }
            }
        }
        return '-';
    }

    public function getInputHTML($langID = null){
        return $this->getSeeValue($langID) . $this->inputType('hidden', $langID);
    }

    /**
     * from witch table has to load options?
     * @return array
     */
    protected function getOptions(){
        $lateralTable = str_replace('id_', '', $this->fieldName);
        return $this->loadOptions($lateralTable, ', created');
    }

}