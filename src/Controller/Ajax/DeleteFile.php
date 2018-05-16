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
        $itemID = $this->getParam('itemID');
        $fileID = $_POST['fieldID'];
        $fieldName = $_POST['fieldName'];

        $this->content->getTable();

        $file = new File($fileID);
        $error = !$file->delete($this->content->getTable(), $fieldName, $itemID, $fileID);
        $this->setError( $error );
    }

}