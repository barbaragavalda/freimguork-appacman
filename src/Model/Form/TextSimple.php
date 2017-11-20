<?php

namespace Appacman\Model\Form;

class TextSimple extends Textarea  {

    public function getInputHTML($langID = null){
        $this->class = 'wysiwyg-textarea-simple';
        return parent::getInputHTML($langID);
    }

}