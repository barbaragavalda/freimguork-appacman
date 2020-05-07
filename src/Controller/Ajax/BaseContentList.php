<?php

namespace Appacman\Controller\Ajax;

use Appacman\Model\Utils\Permissions;

abstract class BaseContentList extends Ajax {

    public function __construct(){
        parent::__construct();

        $this->permission = Permissions::SEE;
    }

    protected function run(){
        $this->removeInfo();

        $itemsPerPage = $_GET['length'];
        $page = ($_GET['start'] / $itemsPerPage) + 1;

        $listType = $this->content->getListType();
        $listClass = 'Appacman\\Model\\Lists\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $listType) ));
        $model = new $listClass($this->content, $page, $itemsPerPage);
        $model->filter($_GET['search']['value'], $_GET['order']);
        $list = $model->getItemsPage();
        $list = $this->extraFields($list);
        foreach($list as $key => &$item){
            $item['actions'] = '[APPACMAN_ACCIONS]';
        }

        $allItems = $model->getAll();
        $this->assign('draw', $_GET['draw'] + 1);
        $this->assign('recordsTotal', count($allItems));
        $this->assign('recordsFiltered', count($allItems));
        $this->assign('data', $list);

        $this->json();
    }

    /**
     * append extra fields to items (if necessary)
     * @param $list     array of current list
     * @return array    modified list
     */
    abstract protected function extraFields($list);

}