<?php

namespace Appacman\Model\Form;

class ImageSeeOnly extends Image {

    /**
     * if there is han image: display it and option to delete it
     * else: show file picker
     * @param null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        if( $this->fileURL == null ){
            return $this->inputType('file', $langID);
        }else{
            return '
                ' . $this->getFile() . '
                <a href="'.$this->fileURL.'" class="btn bg-purple btn-xs" title="'.gettext('Descargar').'" download target="_blank">
                    <i class="fa fa-download"></i>
                </a>
            ';
        }
    }

    public function canSave($langID = null){
        return false;
    }

    public function hasError($langID = null){
        return false;
    }

}