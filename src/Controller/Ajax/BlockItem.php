<?php

namespace Appacman\Controller\Ajax;

use Appacman\Model\Utils\Permissions;
use Core\Controller\CacheManager;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;

#[Route('/bloquear/{contentID}/{itemID}')]
class BlockItem extends Ajax
{

    /**
     * @var int locked state
     */
    protected int $state = 0;

    public function __construct(Config $config, CacheManager $modelCache)
    {
        parent::__construct($config, $modelCache);

        $this->permission = Permissions::LOCK;
    }

    protected function run(): void
    {
        if (isset($_POST['state'])) {
            $this->state = $_POST['state'];
        }
        $this->setError(!$this->item->block($this->state));
    }

}