<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\Ajax\BaseContentList;
use Appacman\Model\Push\Notifier;
use Core\Routing\Attribute\Route;

#[Route('/push-table/{contentID}', methods: ['GET', 'POST'])]
class AjaxTablePushList extends BaseContentList
{

    public function extraFields($list, $assign = true): array
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