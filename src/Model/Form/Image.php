<?php

namespace Appacman\Model\Form;

class Image extends GenericFile {

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

}