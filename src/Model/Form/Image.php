<?php

namespace Appacman\Model\Form;

use Core\Model\File;
use Core\Utils\Exception;

class Image extends FormInput {

    /**
     * @var int $id. Image id
     */
    private $imageID = null;

    /**
     * @var string $imageURL. Image Path
     */
    private $imageURL = null;

    /**
     * initialize image and save its URL
     * @param $info
     * @param $id
     * @param string|null $table
     */
    public function __construct($info, $id, $table){
        parent::__construct($info, $id, $table);

        $this->imageID = parent::getSeeValue();
        $image = new File($this->imageID);
        $this->imageURL = $image->getAbsolutePath();
    }

    public function getListValue(){
        if( $this->imageURL == null ){
            return '-';
        }else{
            return $this->getImage('small');
        }
    }

    /**
     * show image on form
     * @param int|null $langID
     * @return string
     */
    public function getSeeValue($langID = null){
        if( $this->imageURL == null ){
            return '-';
        }else{
            return $this->getImage();
        }
    }

    /**
     * if there is han image: displayit and option to delete it
     * else: show file picker
     * @param null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        if( $this->imageURL == null ){
            return $this->inputType('file', $langID);
        }else{
            return '
                ' . $this->getImage() . '
                <a href="#" data-id="'. $this->imageID.'" data-name="'.$this->fieldName.'" class="btn btn-danger btn-xs delete-image" title="'.gettext('Eliminar').'" data-toggle="confirmation">
                    <i class="fa fa-trash"></i>
                </a>
            ';
        }
    }

    /**
     * only can save image if its set on form
     * @return bool
     */
    public function canSave(){
        if( isset($_FILES[$this->fieldName]) && !empty($_FILES[$this->fieldName]['tmp_name']) ){
            return true;
        }
        return false;
    }

    /**
     * saves image on disk and return its database id
     * @param int|null $langID
     * @return false|int
     * @throws Exception
     */
    protected function getPostValue($langID = null){
        $image = new File();
        $imageID = $image->save( $_FILES[$this->fieldName] );

        if( $imageID === false ){
            throw new Exception('Unable to save image <pre>' . print_r($_FILES[$this->fieldName], true) . '</pre>');
        }
        $this->imageID = $imageID;
        $this->imageURL = $image->getAbsolutePath();
        return $imageID;
    }

    /**
     * image tag with link to see it bigger
     * @param string $class
     * @return string
     */
    private function getImage($class = ''){
        return '
            <a href="'.$this->imageURL.'" class="pull-left media-object" target="_blank">
                <img src="'.$this->imageURL.'" />
            </a>
        ';
    }

}