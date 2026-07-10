<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\ContentList;
use Core\Controller\CacheManager;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;
use Core\Utils\Session;

#[Route('/notificaciones-push/{contentID}')]
class PushList extends ContentList
{

    public function __construct(Config $config, CacheManager $modelCache, Session $session)
    {
        parent::__construct($config, $modelCache, $session);

        $this->listLink = _('notificaciones-push');
        $this->formLink = _('notificacion-push');
    }

    protected function hasPermission(): bool
    {
        $hasPermission = parent::hasPermission();
        $this->listURL = $this->domain . 'push-table/' . $this->content->getID();
        return $hasPermission;
    }

    public function extraHeaders(): array
    {
        return array(
            array(
                'name'       => _('Alcance'),
                'field_name' => 'target'
            )
        );
    }

}