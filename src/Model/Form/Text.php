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
                <textarea name="'.$this->getInputName($langID).'" placeholder="'.$this->getPlaceholder().'">'. parent::getInputValue($langID) .'</textarea>
            </div>
        ';
    }

    /**
     * Check if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        $postValue = $this->getPostValue($langID);
        if( $postValue == null && $this->isRequired ){
            return gettext('Campo obligatorio.');
        }
        return false;
    }

}