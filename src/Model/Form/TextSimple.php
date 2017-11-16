<?php

namespace Appacman\Model\Form;

class TextSimple extends Textarea  {

    public function getInputHTML($langID = null){
        $this->class .= '-simple';
        return parent::getInputHTML($langID = null);
    }

}