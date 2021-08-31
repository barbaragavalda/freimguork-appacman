<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\Ajax\BaseContentList;
use Appacman\Model\Push\Notifier;

class AjaxTablePushList extends BaseContentList
{

    public function extraFields($list, $assign = true)
    {
        foreach ($list as &$item) {
            if (strtolower($item['is_sent']) == 'no' or !$item['is_sent']) {
                $pushID = $item['id'];

                $notifier       = new Notifier();
                $item['target'] = $notifier->getTarget(null, $pushID);
            } else {
                $item['canDelete'] = false;
                $item['canEdit']   = false;
                $item['canSee']    = true;
            }
        }

        return $list;
    }
}