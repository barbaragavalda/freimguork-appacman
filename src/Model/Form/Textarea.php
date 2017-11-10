<?php

namespace Appacman\Model\Form;

class Textarea extends FormInput {

    protected $class = 'wysiwyg-textarea';

    public function getValue(){
        return strip_tags(parent::getValue());
    }

    public function getHTML(){
        return '<div class="'.$this->class.'"><textarea placeholder="'.$this->getName().'">'. parent::getValue() .'</textarea></div>';
    }

}