<?php

namespace Appacman\Controller\Ajax;

use Appacman\Model\Utils\Permissions;
use Core\Controller\CacheManager;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;
use Core\Utils\Session;

#[Route('/eliminar-item/{contentID}/{itemID}', methods: ['GET', 'POST'])]
class DeleteItem extends Ajax
{

    public function __construct(Config $config, CacheManager $modelCache, Session $session)
    {
        parent::__construct($config, $modelCache, $session);

        $this->permission = Permissions::DELETE;
    }

    protected function run(): void
    {
        $success = $this->item->delete();
        $this->setError(!$success);
    }

}