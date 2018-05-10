<?php

namespace Appacman\Model;

use Appacman\Model\Form\Select;
use Core\Model\Model;

class PushForm extends Model {

    public function getInputPlatform($name, $fieldName){
        $input = new Select(
            array(
                'name' => $name,
                'field_name' => $fieldName,
                'value' => null,
                'required' => false
            ),
            null
        );
        r($input);
    }

}