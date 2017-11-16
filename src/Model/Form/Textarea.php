<?php

namespace Appacman\Model\Form;

class Textarea extends FormInput {

    protected $class = 'wysiwyg-textarea';

    public function getValue($langID = null){
        return strip_tags(parent::getValue($langID));
    }

    public function getInputHTML($langID = null){
        return '<div class="'.$this->class.'"><textarea name="'.$this->getFieldName().'" placeholder="'.$this->getName().'">'. parent::getValue($langID) .'</textarea></div>';
    }

}