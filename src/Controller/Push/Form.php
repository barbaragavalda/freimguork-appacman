<?php

namespace Appacman\Controller\Push;

use Appacman\Controller\BaseContentForm;
use Appacman\Model\Push\Statistic;
use Core\Controller\CacheManager;
use Core\Routing\Attribute\Route;
use Core\Utils\Config;

#[Route('/notificacion-push/{contentID}')]
#[Route('/notificacion-push/{contentID}/{itemID}')]
class Form extends BaseContentForm
{

    public function __construct(Config $config, CacheManager $modelCache)
    {
        parent::__construct($config, $modelCache);

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