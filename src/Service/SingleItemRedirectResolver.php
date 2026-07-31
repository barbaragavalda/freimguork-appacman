<?php

namespace Appacman\Service;

use Appacman\Model\Content;

/**
 * Decides whether a content list holding exactly one item should skip
 * straight to that item's form instead of showing the (pointless) list -
 * pulled out of BaseContentList::run() so the dynamic list-class resolution
 * and the "is there really only one item" check can be tested without Twig.
 */
final class SingleItemRedirectResolver
{

    public function resolve(array $menu, Content $content, string $listType): int|string|null
    {
        $currentContent = $this->findMenuEntry($menu, $content->getID());
        if ($currentContent === null || $currentContent['counter'] != 1) {
            return null;
        }

        $listClass = 'Appacman\\Model\\Lists\\' . (str_replace('-', ' ', $listType)
                |> ucwords(...)
                |> (fn($x) => str_replace(' ', '', $x)));
        $model = new $listClass($content, 1, 1);
        $list  = $model->getItemsPage();

        if (!count($list)) {
            return null;
        }

        return $list[0]['id'];
    }

    private function findMenuEntry(array $menu, int $contentID): ?array
    {
        foreach ($menu as $block) {
            foreach ($block['list'] as $entry) {
                if ($entry['id_appacman_content'] == $contentID) {
                    return $entry;
                }
            }
        }
        return null;
    }

}
