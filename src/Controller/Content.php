<?php

namespace Appacman\Controller;

class Content extends AppacmanController {

    /**
     * @var \Appacman\Model\Content $content
     */
    protected $content = null;

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

    protected function getTitle(){
        return '';
    }

    protected function getBreadcrumb(){
        return array();
    }

}