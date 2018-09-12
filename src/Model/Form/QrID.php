<?php

namespace Appacman\Model\Form;

use Core\Model\File;
use Core\Utils\Config;

class QrID extends ImageSeeOnly {

    protected function getInputHTML($langID = null){
        if( $this->fileURL == null ){
            return gettext('El QR se generará automáticamente al guardar.');
        }else{
            return $this->getImage();
        }
    }

    public function hasError($langID = null){
        return false;
    }

    public function canSave($langID = null){
        return true;
    }

    public function save($itemID, $langID = null){
        if( $this->fileURL == null ){
            $config = Config::getInstance();
            $webserviceConfig = $config->get('webservice');
            $urlScheme = $webserviceConfig['url_scheme'];

            $file = new File();
            $qr = $file->saveQr($urlScheme . 'club?id=' . $itemID, 'club-qr-'.$itemID.'.png', 1500);
            if( $qr ){

                $sql = '
                    UPDATE ' . $this->table . '
                    SET ' . $this->fieldName . ' = :qr
                    WHERE id_' . $this->table . ' = :id
                ';
                $params = array(
                    'qr' => array('value' => $qr,       'type' => \PDO::PARAM_INT),
                    'id' => array('value' => $itemID,   'type' => \PDO::PARAM_INT)
                );
                $this->mysql->query($sql, $params);
                if( !$this->mysql->getState() ){
                    $file->deleteFromDisk();
                    $file->deleteFromFileTable();
                    return gettext('No se ha podido generar el QR.');
                }
            }else{
                return gettext('No se ha podido generar el QR.');
            }
        }

        return false;
    }

}