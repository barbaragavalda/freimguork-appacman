<?php

namespace Appacman\Controller;

use Appacman\Model\Utils\Permissions;

abstract class BaseExport extends Content
{

    protected function run(): void
    {
        parent::run();

        $list = $this->content->getExportList();
        $list = $this->addExtraInfo($list);
        $this->assign('csv', $list);
        $this->export($this->content->getTable());
    }

    protected function hasPermission(): bool
    {
        $hasPermission = parent::hasPermission();
        if ($hasPermission) {
            $contentID = $this->content->getID();
            $canExport = $this->user->hasPermission($contentID, Permissions::EXPORT);

            // has permissions to export?
            if ($canExport) {
                return true;
            }
        }

        return $hasPermission;
    }

    protected function getTitle(): string
    {
        return _('Exportar') . ' ' . $this->content->getName();
    }

    protected function getBreadcrumb(): array
    {
        return array();
    }

    abstract protected function addExtraInfo($list): array;

}
