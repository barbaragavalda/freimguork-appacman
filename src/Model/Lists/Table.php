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
        if( !$this->forMenu ) {
            $items = $this->prepare($items);
        }

        return $items;
    }

    public function filter($query, $orders){
        $this->prepared = false;

        if( count($this->items) && !empty($query) ){
            $this->query = StringUtils::removeAccents(mb_strtolower($query));
            $this->prepareItems();

            $items = array();
            foreach($this->items as $item){
                if( count(array_filter($item, array($this, 'search'))) ){
                    $items[] = $item;
                }
            }
            $this->items = $items;
        }

        if( count($this->items) && !empty($orders) ) {
            $this->prepareItems();
            $auxItems = $this->items;
            $keys = array_keys(array_shift($auxItems));
            foreach ($orders as $order) {
                $this->initOrderField($order, $keys);
                $this->orderDirection = $order['dir'];
                usort($this->items, array($this, 'order'));
            }
        }
    }

    protected function initOrderField($order, $keys){
        $this->orderField = $keys[$order['column'] + 1];
    }

    private function prepareItems(){
        if( count($this->items) < 600 ){
            $this->items = $this->prepare($this->items);
            $this->prepared = true;
        }
    }

    public function search($value){
        if( is_array($value) ){
            return count(array_filter($value, array($this, 'search')));
        }
        return strpos(StringUtils::removeAccents(mb_strtolower($value)), $this->query) !== false;
    }

    public function order($a, $b) {
        $valueA = StringUtils::removeAccents($a[$this->orderField]);
        $valueB = StringUtils::removeAccents($b[$this->orderField]);
        if( is_array($this->orderField) ){
            $valueA = $a;
            $valueB = $b;
            foreach ($this->orderField as $order){
                $valueA = $valueA[$order];
                $valueB = $valueB[$order];
            }
        }

        if( is_numeric($valueA) && is_numeric($valueB) ){
            if( $valueA == $valueB ) return 0;
            if( $this->orderDirection == 'asc' ) {
                return $valueA < $valueB ? -1 : 1;
            }
            return $valueB < $valueA ? -1 : 1;
        }else{
            if( $this->orderDirection == 'asc' ){
                return strcmp($valueA, $valueB);
            }
            return strcmp($valueB, $valueA);
        }
    }

    protected function prepare($items){
        if( !$this->prepared ) {
            foreach ($items as &$row) {
                foreach ($this->fields as $field) {
                    $input                         = $this->content->getInputClass($field, $row);
                    $row[ $input->getFieldName() ] = $input->getListValue();
                }
                if (array_key_exists('is_locked', $row)) {
                    $isLocked = $row['is_locked'];
                    unset($row['is_locked']);
                    $row['is_locked'] = $isLocked;
                }
            }
        }
        return $items;
    }

}