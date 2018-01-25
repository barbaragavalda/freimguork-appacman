<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;

class SelectEncryptedTwoWay extends Select  {

    public function getSeeValue($langID = null){
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

    /**
     * from witch table has to load options?
     * @return array
     */
    protected function getOptions(){
        $lateralTable = str_replace('id_', '', $this->fieldName);
        return $this->loadOptions($lateralTable, ', created');
    }

}