<?php

namespace Appacman\Model\Form;

use Core\Model\File;
use Core\Utils\Exception;

class GenericFileSeeOnly extends GenericFile {

    /**
     * if there is han image: displayit and option to delete it
     * else: show file picker
     * @param null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        return $this->getSeeValue($langID);
    }

    /**
     * CANNOT save
     * @return bool
     */
    public function canSave($langID = null){
        return false;
    }

    public function hasError($langID = null){
        return false;
    }

    public function save($itemID, $langID = null){
        return false;
    }

}