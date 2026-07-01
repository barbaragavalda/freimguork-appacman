<?php

namespace Appacman\Controller\Ajax;

class ContentList extends BaseContentList
{

    protected function extraFields($list): array
    {
        return $list;
    }

}