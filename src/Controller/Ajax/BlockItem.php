<?php

namespace Appacman\Controller\Ajax;

use Appacman\Model\Utils\Permissions;

class BlockItem extends Ajax
{

    /**
     * @var int locked state
     */
    protected int $state = 0;

    public function __construct()
    {
        parent::__construct();

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