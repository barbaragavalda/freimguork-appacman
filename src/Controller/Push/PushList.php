<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\ContentList;
use Appacman\Model\Push\Notifier;

class PushList extends ContentList  {

    protected function hasPermission() {
        $this->params['contentID'] = $this->parts[1];
        return parent::hasPermission();
    }

    public function extraHeaders(){
        return array(
            array(
                'name' => gettext('Alcance'),
                'field_name'    => 'target'
            )
        );
    }

    public function extraFields($list, $assign = true){
        foreach($list as &$item){
            if( strtolower($item['is_sent']) == 'no' ){
                $pushID = $item['id'];

                $notifier = new Notifier();
                $item['target'] = $notifier->getTarget(null, $pushID);
            }
        }

        return $list;
    }

}