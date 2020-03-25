<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\ContentList;
use Appacman\Model\Push\Notifier;

class PushList extends ContentList  {

    public function __construct(){
        parent::__construct();

        $this->listLink = _('notificaciones-push');
        $this->formLink = _('notificacion-push');
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
            if( strtolower($item['is_sent']) == 'no' or !$item['is_sent'] ){
                $pushID = $item['id'];

                $notifier = new Notifier();
                $item['target'] = $notifier->getTarget(null, $pushID);
            }else{
                $item['canDelete'] = false;
                $item['canEdit'] = false;
                $item['canSee'] = true;
            }
        }

        return $list;
    }

}