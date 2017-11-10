<?php

namespace Appacman\Model\Form;

class TextSimple extends Textarea  {

    public function getHTML(){
        $this->class .= '-simple';
        return parent::getHTML();
    }

}