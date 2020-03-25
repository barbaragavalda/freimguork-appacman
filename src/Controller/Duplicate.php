<?php

namespace Appacman\Controller;

use Appacman\Model\Item;
use Appacman\Model\Utils\Language;
use Appacman\Model\Utils\Permissions;
use Core\Model\File;

class Duplicate extends Content {

    /**
     * @var \Appacman\Model\Item $item
     */
    protected $item = null;

    protected function run(){
        parent::run();

        // languages
        $languages = array();
        if( $this->item->hasLang() ){
            $lang = new Language();
            $languages = $lang->get();
        }

        $inputs = $this->item->get($languages);
        $inputsHTML = '';
        foreach($inputs as $input){
            $fieldName = $input->getFieldName();
            /*
            if( is_a($input, 'Appacman\\Model\\Form\\Image') ){
                $file = new File($input->getValue());
                $path = $file->getRelativePath();
                $photoID = $file->copy(basename($path), $path);
                $inputsHTML .= '<input type="hidden" name="' . $fieldName . '" value="' . $photoID . '" />';
            }else*/ if( in_array($fieldName, array('start', 'end', 'last_update', 'created')) === false ){
                $inputsHTML .= '<input type="hidden" name="' . $fieldName . '" value="' . $input->getValue() . '" />';
            }
        }
        echo '
            <form id="duplicate" action="' . $this->domain . $this->formLink . '/' . $this->content->getID() . '" method="POST">
                ' . $inputsHTML . '
                <input type="hidden" name="send" value="1" />
            </form>
            <script type="text/javascript">
                document.getElementById("duplicate").submit();
            </script>
        ';
        exit;
    }

    protected function hasPermission(){
        $hasPermission = parent::hasPermission();

        if( $hasPermission ){
            $contentID = $this->content->getID();
            $canDuplicate = $this->user->hasPermission($contentID, Permissions::DUPLICATE);
            if( $canDuplicate ){
                // has permission to create?
                $itemID = $this->getParam('itemID');
                $this->item = new Item($itemID, $this->content->getTable());
                if( !$this->item->exists() ){
                    $hasPermission = false;
                }
            }else{
                $hasPermission = false;
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