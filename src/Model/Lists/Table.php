<?php

namespace Appacman\Model\Lists;

use Core\Model\Paginated;
use Core\Model\Utils\StringUtils;

class Table extends Paginated {

    /**
     * @var \Appacman\Model\Content $content
     */
    protected $content = null;

    /**
     * @var bool
     */
    protected $forMenu = false;

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var string
     */
    protected $query = '';

    /**
     * @var boolean
     */
    protected $prepared = false;

    /**
     * @var string
     */
    protected $orderField = '';

    /**
     * @var string
     */
    protected $orderDirection = '';

    public function __construct($content, $page = 1, $itemsPerPage = 25, $forMenu = false){
        $this->content = $content;
        $this->forMenu = $forMenu;

        parent::__construct($page, $itemsPerPage);
    }

    public function initAll(){
        $list = $this->content->get();
        $this->items = $list['rows'];
        $this->fields = $list['fields'];
    }

    /**
     * return only items on current page
     * @return array
     */
    public function getItemsPage(){
        $items = parent::getItemsPage();

        // prepare rows for list
        if( !$this->prepared && !$this->forMenu ) {
            $items = $this->prepare($items);
        }

        return $items;
    }

    public function filter($query, $orders){
        $this->prepared = false;
        if( count($this->items) && !empty($query) ){
            $this->query = StringUtils::removeAccents(mb_strtolower($query));

            if( count($this->items) < 500 ){
                $this->prepared = true;
                $this->items = $this->prepare($this->items);
            }

            $items = array();
            foreach($this->items as $item){
                if( count(array_filter($item, array($this, 'search'))) ){
                    $items[] = $item;
                }
            }
            $this->items = $items;
        }

        if( count($this->items) && !empty($orders) ) {
            $keys = array_keys($this->items[0]);
            foreach ($orders as $order) {
                $this->orderField = $keys[$order['column'] + 1];
                $this->orderDirection = $order['dir'];
                usort($this->items, array($this, 'order'));
            }
        }
    }

    public function search($value){
        return strpos(StringUtils::removeAccents(mb_strtolower($value)), $this->query) !== false;
    }

    public function order($a, $b) {
        if( $this->orderDirection == 'asc' ){
            return strcmp($a[$this->orderField], $b[$this->orderField]);
        }
        return strcmp($b[$this->orderField], $a[$this->orderField]);
    }

    private function prepare($items){
        foreach( $items as &$row ){
            foreach( $this->fields as $field ){
                $input = $this->content->getInputClass($field, $row);
                $row[ $input->getFieldName() ] = $input->getListValue();
            }
            if( array_key_exists('is_locked', $row) ){
                $isLocked = $row['is_locked'];
                unset($row['is_locked']);
                $row['is_locked'] = $isLocked;
            }
        }
        return $items;
    }

}