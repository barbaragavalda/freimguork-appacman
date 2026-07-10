<?php

namespace Appacman\Controller\Ajax;

use Appacman\Model\Utils\Permissions;
use Core\Controller\CacheManager;
use Core\Utils\Config;

abstract class BaseContentList extends Ajax
{

    public function __construct(Config $config, CacheManager $modelCache)
    {
        parent::__construct($config, $modelCache);

        $this->permission = Permissions::SEE;
    }

    protected function run(): void
    {
        $this->removeInfo();

        $itemsPerPage = $_REQUEST['length'];
        $page         = ($_REQUEST['start'] / $itemsPerPage) + 1;

        $order  = $_REQUEST['list_order'] ?? array();
        $search = $_REQUEST['search']['value'] ?? '';

        $listType  = $this->content->getListType();
        $listClass = 'Appacman\\Model\\Lists\\' . str_replace('-', ' ', $listType)
                |> ucwords(...)
                |> (fn($x) => str_replace(' ', '', $x));
        $model     = new $listClass($this->content, $page, $itemsPerPage);
        $model->filter($search, $order);
        $list = $model->getItemsPage();
        $list = $this->extraFields($list);
        foreach ($list as &$item) {
            if (empty($item['id'])) {
                $item['actions'] = '';
            } else {
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
     *
     * @param $list     array of current list
     *
     * @return array    modified list
     */
    abstract protected function extraFields(array $list): array;

}