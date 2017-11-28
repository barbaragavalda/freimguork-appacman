<?php

namespace Appacman\Model\Form;

class Image extends GenericFile {

    private $allowedExtensions = array('png', 'jpg', 'jpeg', 'gif', 'svg');

    /**
     * image tag with link to see it
     * @return string
     */
    protected function getFile(){
        return '
            <a href="'.$this->fileURL.'" class="pull-left media-object" target="_blank">
                <img src="'.$this->fileURL.'" />
            </a>
        ';
    }

    /**
     * Check file extension
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        $error = parent::hasError($langID);
        $postName = parent::getInputName($langID);
        if( !$error && !empty($_FILES[$postName]['tmp_name']) ){
            $postName = parent::getInputName($langID);
            $extension = pathinfo($_FILES[$postName]['name'], PATHINFO_EXTENSION);
            if( !in_array($extension, $this->allowedExtensions) ){
                return str_replace('%types%', implode(', ', $this->allowedExtensions), gettext('La imagen debe ser de tipo %types%'));
            }
        }
        return $error;
    }

}