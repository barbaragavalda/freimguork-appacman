<?php

namespace Appacman\Controller;

use Appacman\Model\Utils\Permissions;

abstract class BaseContentList extends Content {

    protected $redirectToForm = true;

    protected function run(){
        parent::run();

        $template = true;
        $listType = $this->content->getListType();

        if( $this->redirectToForm ){
            $contentID = $this->content->getID();
            $currentContent = null;
            foreach($this->info['menu'] as $block){
                foreach($block['list'] as $content){
                    if( $content['id_appacman_content'] == $contentID ){
                        $currentContent = $content;
                        break;
                    }
                }
            }

            if( $currentContent && $currentContent['counter'] == 1 ){
                // only one item
                $listClass = 'Appacman\\Model\\Lists\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $listType) ));
                $model = new $listClass($this->content, 1, 1);
                $list = $model->getItemsPage();
                if( count($list) ){
                    $template = false;
                    $this->redirect($this->domain . _('formulario') . '/' . $contentID  . '/' . $list[0]['id']);
                }
            }
        }

        if( $template ){
            // list configuration
            $headers = array_merge($this->content->getTableHeaders(), $this->extraHeaders());
            $this->assign('list_headers', $headers);
            $this->assign('list_order', $this->content->getOrderBy());
            $this->assign('tableData', $_POST);

            $this->template('List/' . $listType . '.twig');
        }
    }

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();
        if( $hasPermission ){
            $contentID = $this->content->getID();
            $canSee = $this->user->hasPermission($contentID, Permissions::SEE);
            $canEdit = $this->user->hasPermission($contentID, Permissions::EDIT);
            $canCreate = $this->user->hasPermission($contentID, Permissions::CREATE);
            $canDelete = $this->user->hasPermission($contentID, Permissions::DELETE);
            $canExport = $this->user->hasPermission($contentID, Permissions::EXPORT);
            $canLock = $this->user->hasPermission($contentID, Permissions::LOCK);
            $canOwn = $this->user->hasPermission($contentID, Permissions::OWN);
            $canDuplicate = $this->user->hasPermission($contentID, Permissions::DUPLICATE);
            $canLogOut = $this->user->hasPermission($contentID, Permissions::LOG_OUT);

            // has permissions to see list?
            if( $canSee || $canEdit || $canCreate || $canDelete || $canExport || $canLock || $canOwn || $canDuplicate || $canDuplicate ){
                $this->assign('canSee', $canSee);
                $this->assign('canEdit', $canEdit);
                $this->assign('canCreate', $canCreate);
                $this->assign('canDelete', $canDelete);
                $this->assign('canExport', $canExport);
                $this->assign('canLock', $canLock);
                $this->assign('canOwn', $canOwn);
                $this->assign('canDuplicate', $canDuplicate);
                $this->assign('canLogOut', $canLogOut);
            }else{
                $hasPermission = false;
            }
        }

        return $hasPermission;
    }

    protected function getTitle(){
        return gettext('Listado') . ' ' . $this->content->getName();
    }

    protected function getBreadcrumb(){
        return array(
            array('name' => $this->content->getName(), 'link' => null)
        );
    }

    /**
     * append extra columns (if necessary)
     * @return array    array of extra fields array( array('name' => '<display_name>', 'field_name' => '<field>') )
     */
    abstract protected function extraHeaders();

}