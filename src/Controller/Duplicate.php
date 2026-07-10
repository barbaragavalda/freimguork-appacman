<?php

namespace Appacman\Controller;

use Appacman\Model\Item;
use Appacman\Model\Utils\Language;
use Appacman\Model\Utils\Permissions;
use Core\Routing\Attribute\Route;

#[Route('/duplicar/{contentID}/{itemID}', methods: ['GET', 'POST'])]
class Duplicate extends Content
{

    protected ?Item $item = null;

    protected function run(): void
    {
        parent::run();

        // languages
        $languages = array();
        if ($this->item->hasLang()) {
            $lang      = new Language();
            $languages = $lang->get();
        }

        $inputs     = $this->item->get($languages);
        $inputsData = array();
        foreach ($inputs as $input) {
            $fieldName = $input->getFieldName();
            if (in_array($fieldName, array('start', 'end', 'last_update', 'created')) === false) {
                $inputsData[] = array('name' => $fieldName, 'value' => $input->getValue());
            }
        }
        $this->assign('action', $this->domain . $this->formLink . '/' . $this->content->getID());
        $this->assign('inputs', $inputsData);
        $this->template('duplicate.twig');
    }

    protected function hasPermission(): bool
    {
        $hasPermission = parent::hasPermission();

        if ($hasPermission) {
            $contentID    = $this->content->getID();
            $canDuplicate = $this->user->hasPermission($contentID, Permissions::DUPLICATE);
            if ($canDuplicate) {
                // has permission to create?
                $itemID     = $this->getParam('itemID');
                $this->item = new Item($itemID, $this->content->getTable());
                if (!$this->item->exists()) {
                    $hasPermission = false;
                }
            } else {
                $hasPermission = false;
            }
        }

        return $hasPermission;
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