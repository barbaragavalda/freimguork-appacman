<?php

namespace Appacman\Controller\Ajax\Dynamic;

use Appacman\Controller\Ajax\Ajax;
use Appacman\Model\Item;
use Appacman\Model\Utils\Permissions;

class Delete extends Ajax
{

    public function __construct()
    {
        parent::__construct();

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