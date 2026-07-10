<?php

namespace Appacman\Controller\Ajax\Dynamic;

use Appacman\Controller\Ajax\Ajax;
use Appacman\Model\Form\Dynamic;
use Appacman\Model\Utils\Language;
use Appacman\Model\Utils\Permissions;
use Core\Controller\CacheManager;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;

#[Route('/anadir-campo/{contentID}')]
#[Route('/anadir-campo/{contentID}/{itemID}')]
class Add extends Ajax
{

    public function __construct(Config $config, CacheManager $modelCache)
    {
        parent::__construct($config, $modelCache);

        $this->permission = Permissions::EDIT;
    }

    protected function run(): void
    {
        $field    = new Dynamic(
            array(
                'field_name' => $_POST['field'],
                'name'       => '',
                'value'      => '',
                'required'   => false,
                'type'       => 'dynamic'
            ), $_POST['id'], $_POST['table']
        );
        $position = null;
        if ($_POST['position']) {
            $position = $_POST['position'];
        }

        $languagesModel = new Language();
        $languages      = $languagesModel->get();
        $field->setLanguages($languages);

        $this->removeInfo();
        $this->assign('html', $field->getItemHTML(null, true, $position));
        $this->json();
    }

}