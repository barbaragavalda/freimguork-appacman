<?php

namespace Appacman\Controller;

use Appacman\Model\Utils\Permissions;
use Appacman\Service\CrudPermissions;
use Appacman\Service\SingleItemRedirectResolver;

abstract class BaseContentList extends Content
{

    protected bool $redirectToForm = true;

    protected bool $hasSearch = true;

    protected ?string $listURL = null;

    protected function run(): void
    {
        parent::run();

        $listType  = $this->content->getListType();
        $contentID = $this->content->getID();

        if ($this->redirectToForm) {
            $itemID = (new SingleItemRedirectResolver())->resolve($this->info['menu'], $this->content, $listType);
            if ($itemID !== null) {
                $link = _('formulario');
                if ($this->content->getTable() == 'appacman_push') {
                    $link = _('notificacion-push');
                }
                $this->redirect($this->domain . $link . '/' . $contentID . '/' . $itemID);
                return;
            }
        }

        if ($this->listURL == null) {
            $this->listURL = $this->domain . 'table/' . $contentID;
        }

        // list configuration
        $headers = array_merge($this->content->getTableHeaders(), $this->extraHeaders());
        $this->assign('has_search', $this->hasSearch);
        $this->assign('list_url', $this->listURL);
        $this->assign('list_headers', $headers);
        $this->assign('list_order', $this->content->getOrderBy());
        $this->assign('tableData', $_POST);

        $this->template('List/' . $listType . '.twig');
    }

    protected function hasPermission(): bool
    {
        $hasPermission = parent::hasPermission();
        if ($hasPermission) {
            $contentID    = $this->content->getID();
            $permissions  = CrudPermissions::resolve($this->user, $contentID);
            $canExport    = $this->user->hasPermission($contentID, Permissions::EXPORT);
            $canDuplicate = $this->user->hasPermission($contentID, Permissions::DUPLICATE);
            $canLogOut    = $this->user->hasPermission($contentID, Permissions::LOG_OUT);

            // has permissions to see list?
            if ($permissions->canSee || $permissions->canEdit || $permissions->canCreate
                || $permissions->canDelete || $canExport || $permissions->canLock
                || $permissions->canOwn || $canDuplicate) {
                $this->assign('canSee', $permissions->canSee);
                $this->assign('canEdit', $permissions->canEdit);
                $this->assign('canCreate', $permissions->canCreate);
                $this->assign('canDelete', $permissions->canDelete);
                $this->assign('canExport', $canExport);
                $this->assign('canLock', $permissions->canLock);
                $this->assign('canOwn', $permissions->canOwn);
                $this->assign('canDuplicate', $canDuplicate);
                $this->assign('canLogOut', $canLogOut);
            } else {
                $hasPermission = false;
            }
        }

        return $hasPermission;
    }

    protected function getTitle(): string
    {
        return _('Listado') . ' ' . $this->content->getName();
    }

    protected function getBreadcrumb(): array
    {
        return array(
            array('name' => $this->content->getName(), 'link' => null)
        );
    }

    /**
     * append extra columns (if necessary)
     * @return array    array of extra fields array( array('name' => '<display_name>', 'field_name' => '<field>') )
     */
    abstract protected function extraHeaders(): array;

}