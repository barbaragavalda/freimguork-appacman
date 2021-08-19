<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\ContentList;

class PushList extends ContentList  {

    public function __construct(){
        parent::__construct();

        $this->listLink = _('notificaciones-push');
        $this->formLink = _('notificacion-push');
    }

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();
        $this->listURL = $this->domain . 'push-table/' . $this->content->getID();
        return $hasPermission;
    }

    public function extraHeaders(){
        return array(
            array(
                'name' => gettext('Alcance'),
                'field_name'    => 'target'
            )
        );
    }

}