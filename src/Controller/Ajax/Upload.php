<?php

namespace Appacman\Controller\Ajax;

use Appacman\Model\Utils\Permissions;
use Core\Controller\CacheManager;
use Core\Model\File;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;

#[Route('/subir-archivo/{contentID}')]
#[Route('/subir-archivo/{contentID}/{itemID}')]
class Upload extends Ajax
{

    public function __construct(Config $config, CacheManager $modelCache)
    {
        parent::__construct($config, $modelCache);

        $this->permission = Permissions::EDIT;
    }

    protected function run(): void
    {
        $this->removeInfo();

        $error = false;
        if (count($_FILES)) {
            $path = array();
            foreach ($_FILES as $uploadedFile) {
                $file = new File();
                $file->save($uploadedFile);
                if ($file->getID()) {
                    $file->resize(array(
                        array(
                            'suffix' => 'text',
                            'width'  => 2000,
                            'height' => 2000
                        )
                    ));
                }
                $path[] = $file->getAbsolutePath('text');
            }
            $this->assign('path', $path);
        } else {
            $error = true;
        }

        $this->assign('error', $error);
        $this->json();
    }

}