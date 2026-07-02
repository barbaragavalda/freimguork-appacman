<?php

namespace Appacman\Model;

use Core\Model\File;
use Core\Model\Model;

abstract class HeaderNotifications extends Model
{

    public function get(): array
    {
        return array(
            'counter' => $this->getCounter(),
            'list'    => $this->getList()
        );
    }

    /**
     * total notifications
     * @return int
     */
    abstract public function getCounter(): int;

    /**
     * array list
     * array(
     *      array(
     *          'link' => '<link for appacman page>',
     *          'class' => '<icon and color>',
     *          'title' => '<title>'
     *      )
     * )
     * @return array
     */
    abstract public function getList(): array;

}