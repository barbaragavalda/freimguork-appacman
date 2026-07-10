<?php

namespace Appacman\Controller\Ajax;

use Appacman\Model\Utils\Permissions;
use Core\Controller\CacheManager;
use Core\Model\File;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;

#[Route('/eliminar-archivo/{contentID}/{itemID}')]
class DeleteFile extends Ajax
{

    public function __construct(Config $config, CacheManager $modelCache)
    {
        parent::__construct($config, $modelCache);

        $this->permission = Permissions::EDIT;
    }

    protected function run(): void
    {
        $itemID    = $_POST['itemID'];
        $fileID    = $_POST['fieldID'];
        $fieldName = $_POST['fieldName'];
        $tableName = $_POST['tableName'];

        $file  = new File($fileID);
        $error = !$file->delete($tableName, $fieldName, $itemID);
        $this->setError($error);
    }

}