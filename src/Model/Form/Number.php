<?php

namespace Appacman\Model\Form;

class Number extends FormInput {

    protected $type = \PDO::PARAM_STR;

    /**
     * Post value must be a number
     * @param null $langID
     * @return bool
     */
    protected function getPostValue($langID = null){
        $postName = $this->getInputName($langID, false);
        $post = $this->getPost($postName);

        if( $post === '' ){
            return null;
        }
        return $post;
    }

    /**
     * input type text
     * @param int|null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return $this->inputType('text', $langID);
    }

    public function hasError($langID = null){
        return false;
    }

    public function save($itemID, $langID = null){
        return false;
    }

}