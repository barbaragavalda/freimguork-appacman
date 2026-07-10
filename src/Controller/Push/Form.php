<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\BaseContentForm;
use Appacman\Model\Push\Statistic;
use Core\Controller\CacheManager;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;
use Core\Utils\Session;

#[Route('/notificacion-push/{contentID}', methods: ['GET', 'POST'])]
#[Route('/notificacion-push/{contentID}/{itemID}', methods: ['GET', 'POST'])]
class Form extends BaseContentForm
{

    public function __construct(Config $config, CacheManager $modelCache, Session $session)
    {
        parent::__construct($config, $modelCache, $session);

        $this->listLink = _('notificaciones-push');
        $this->formLink = _('notificacion-push');
    }

    protected function run(): void
    {
        $this->template = 'Push/form.twig';
        parent::run();
    }

    protected function prepareForm(): void
    {
        parent::prepareForm();

        if ($this->item->getID()) {
            foreach ($this->info['form'] as $input) {
                if ($input->getFieldName() == 'is_sent' && filter_var($input->getValue(), FILTER_VALIDATE_BOOL)) {
                    // if is sent: cannot edit or delete
                    $this->assign('canEdit', false);
                    $this->assign('canDelete', false);

                    // and add some statistics
                    $statistics = new Statistic($this->item->getID());
                    $this->assign('statistics', $statistics->get());
                    break;
                }
            }
        }
    }

    protected function getBreadcrumb(): array
    {
        return array(
            array(
                'name' => $this->content->getName(),
                'link' => $this->domain . _('notificaciones-push') . '/' . $this->content->getID()
            ),
            array('name' => $this->item->getName(), 'link' => null)
        );
    }

    protected function hasErrors(): bool
    {
        return false;
    }

}