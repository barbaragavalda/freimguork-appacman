<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\Ajax\Ajax;
use Appacman\Model\Push\Notifier;
use Appacman\Model\Utils\Permissions;
use Core\Controller\CacheManager;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;

#[Route('/push-target/{contentID}')]
class Target extends Ajax
{

    public function __construct(Config $config, CacheManager $modelCache)
    {
        parent::__construct($config, $modelCache);

        $this->permission = Permissions::EDIT;
    }

    protected function run(): void
    {
        $this->removeInfo();

        $notifier = new Notifier();
        $this->assign('target', $notifier->getTarget($_POST));
        $this->json();
    }

}