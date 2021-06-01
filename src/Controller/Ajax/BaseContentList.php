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

        $itemsPerPage = $_REQUEST['length'];
        $page = ($_REQUEST['start'] / $itemsPerPage) + 1;

        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : array();
        $search = isset($_REQUEST['search']) && isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value'] : '';

        $listType = $this->content->getListType();
        $listClass = 'Appacman\\Model\\Lists\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $listType) ));
        $model = new $listClass($this->content, $page, $itemsPerPage);
        $model->filter($search, $order);
        $list = $model->getItemsPage();
        $list = $this->extraFields($list);
        foreach($list as $key => &$item){
            if( empty($item['id']) ){
                $item['actions'] = '';
            }else{
                $item['actions'] = '[APPACMAN_ACCIONS]';
            }
        }

        $allItems = $model->getAll();
        $this->assign('draw', $_REQUEST['draw'] + 1);
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