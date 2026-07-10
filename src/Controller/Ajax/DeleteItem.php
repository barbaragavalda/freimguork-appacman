<?php

namespace Appacman\Controller\Ajax;

use Appacman\Model\Utils\Permissions;
use Core\Controller\CacheManager;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;

#[Route('/eliminar-item/{contentID}/{itemID}')]
class DeleteItem extends Ajax
{

    public function __construct(Config $config, CacheManager $modelCache)
    {
        parent::__construct($config, $modelCache);

        $this->permission = Permissions::DELETE;
    }

    protected function run(): void
    {
        $success = $this->item->delete();
        $this->setError(!$success);
    }

}