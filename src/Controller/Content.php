<?php

namespace Appacman\Controller;

use Appacman\Model\Utils\Language;

class Content extends AppacmanController {

    /**
     * @var \Appacman\Model\Content $content
     */
    protected $content = null;

    /**
     * @var \Appacman\Model\Item $item
     */
    protected $item = null;

    protected function run(){
        $this->assign('contentID', $this->content->getID());
    }

    protected function hasPermission(){
        $hasPermission = false;
        $contentID = intval($this->getParam('contentID'));
        // has content id?
        if( $contentID > 0 ){
            // content exists?
            $this->content = new \Appacman\Model\Content($contentID);
            if( $this->content->exists() ){
                $hasPermission = true;
            }
        }

        return $hasPermission;
    }

    /**
     * @return mixed
     */
    protected function getForm(){
        // languages
        $languages = array();
        if( $this->item->hasLang() ){
            $lang = new Language();
            $languages = $lang->get();
        }
        $this->assign('languages', $languages);

        return $this->item->get($languages);
    }

    protected function getTitle(){
        return '';
    }

    protected function getBreadcrumb(){
        return array();
    }

}