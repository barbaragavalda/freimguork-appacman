<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\Ajax\Ajax;
use Appacman\Model\Push\Notifier;
use Appacman\Model\Utils\Permissions;
use Core\Controller\CacheManager;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;
use Core\Utils\Session;

#[Route('/push-target/{contentID}')]
class Target extends Ajax
{

    public function __construct(Config $config, CacheManager $modelCache, Session $session)
    {
        parent::__construct($config, $modelCache, $session);

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