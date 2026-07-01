<?php

namespace Appacman\Controller;

use Appacman\Model\Item;
use Appacman\Model\Utils\Language;

class Content extends AppacmanController
{

    protected \Appacman\Model\Content|null $content = null;

    protected ?Item $item = null;

    protected ?string $listLink = null;

    protected ?string $formLink = null;

    public function __construct()
    {
        parent::__construct();

        $this->listLink = _('listado');
        $this->formLink = _('formulario');
    }

    protected function run(): void
    {
        $this->assign('contentID', $this->content->getID());
        $this->assign('formLink', $this->formLink);
        $this->assign('listLink', $this->listLink);
    }

    protected function hasPermission(): bool
    {
        $hasPermission = false;
        $contentID     = intval($this->getParam('contentID'));
        // has content id?
        if ($contentID > 0) {
            // content exists?
            $this->content = new \Appacman\Model\Content($contentID);
            if ($this->content->exists()) {
                $hasPermission = true;
            }
        }

        return $hasPermission;
    }

    protected function getForm(): mixed
    {
        // languages
        $languages = array();
        if ($this->item->hasLang()) {
            $lang      = new Language();
            $languages = $lang->get();
        }
        $this->assign('languages', $languages);

        return $this->item->get($languages);
    }

    protected function getTitle(): string
    {
        return '';
    }

    protected function getBreadcrumb(): array
    {
        return array();
    }

}