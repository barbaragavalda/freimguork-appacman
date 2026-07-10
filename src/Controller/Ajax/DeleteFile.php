<?php

namespace Appacman\Controller\Ajax;

use Appacman\Model\Utils\Permissions;
use Core\Controller\CacheManager;
use Core\Model\File;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;
use Core\Utils\Session;

#[Route('/eliminar-archivo/{contentID}/{itemID}')]
class DeleteFile extends Ajax
{

    public function __construct(Config $config, CacheManager $modelCache, Session $session)
    {
        parent::__construct($config, $modelCache, $session);

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