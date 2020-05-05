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

        $_GET = json_decode('{"draw":1,"columns":[{"data":0,"name":"","searchable":true,"orderable":true,"search":{"value":"","regex":false}},{"data":1,"name":"","searchable":true,"orderable":true,"search":{"value":"","regex":false}},{"data":2,"name":"","searchable":true,"orderable":true,"search":{"value":"","regex":false}},{"data":3,"name":"","searchable":true,"orderable":false,"search":{"value":"","regex":false}}],"order":[],"start":0,"length":25,"search":{"value":"","regex":false}}', true);
        $itemsPerPage = $_GET['length'];
        $page = $_GET['start'] / $itemsPerPage;

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
        $this->assign('draw', $page);
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