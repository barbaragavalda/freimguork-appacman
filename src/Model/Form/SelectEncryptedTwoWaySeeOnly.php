<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\TwoWay;
use Core\Utils\Config;

class SelectEncryptedTwoWaySeeOnly extends SelectEncryptedTwoWay {

    public function getInputHTML($langID = null){
        return $this->getSeeValue($langID) . $this->inputType('hidden', $langID);
    }

}