<?php

namespace Appacman\Model\Form;

class Text extends FormInput {

    protected $class = 'wysiwyg-textarea';

    /**
     * remove tags on list
     * @param null $langID
     * @return string
     */
    public function getListValue($langID = null){
        return strip_tags(parent::getListValue($langID));
    }

    /**
     * on list, show text without tags
     * @param int|null $langID
     * @return string
     */
    public function getSeeValue($langID = null){
        return strip_tags(parent::getSeeValue($langID));
    }


    protected function getInputHTML($langID = null){
        return '
            <div class="'.$this->class.'">
                <textarea name="'.$this->getInputName($langID).'" placeholder="'.$this->getName().'">'. parent::getInputValue($langID) .'</textarea>
            </div>
        ';
    }

}