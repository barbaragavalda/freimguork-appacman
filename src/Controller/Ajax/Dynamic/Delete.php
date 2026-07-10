<?php

namespace Appacman\Controller\Ajax\Dynamic;

use Appacman\Controller\Ajax\Ajax;
use Appacman\Model\Item;
use Appacman\Model\Utils\Permissions;
use Core\Controller\CacheManager;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;

#[Route('/eliminar-campo/{contentID}/{itemID}')]
class Delete extends Ajax
{

    public function __construct(Config $config, CacheManager $modelCache)
    {
        parent::__construct($config, $modelCache);

        $this->permission = Permissions::DELETE;
    }

    protected function run(): void
    {
        $item = new Item($_POST['id'], $_POST['field']);
        $item->exists();
        $success = $item->delete();
        $this->setError(!$success);

        $this->json();
    }

}