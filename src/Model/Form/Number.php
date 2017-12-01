<?php

namespace Appacman\Model\Form;

class Number extends FormInput {

    protected $type = \PDO::PARAM_INT;

    /**
     * input type text
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return $this->inputType('text', $langID);
    }

    public function hasError($langID = null){}

}