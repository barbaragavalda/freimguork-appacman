<?php

namespace Appacman\Controller\Ajax;

use Appacman\Model\Utils\Permissions;
use Core\Model\File;

class DeleteFile extends Ajax {

    public function __construct(){
        parent::__construct();

        $this->permission = Permissions::EDIT;
    }

    protected function run(){
        $itemID = $_POST['itemID'];
        $fileID = $_POST['fieldID'];
        $fieldName = $_POST['fieldName'];
        $tableName = $_POST['tableName'];

        $file = new File($fileID);
        $error = !$file->delete($tableName, $fieldName, $itemID, $fileID);
        $this->setError( $error );
    }

}