<?php

namespace Appacman\Model\Form;

use Core\Model\File;
use Core\Utils\Exception;

class OtherFile extends FormInput {

    /**
     * @var int $id. Image id
     */
    protected $fileID = null;

    /**
     * @var string $fileURL. Image Path
     */
    protected $fileURL = null;

    /**
     * initialize image and save its URL
     * @param $info
     * @param $id
     * @param string|null $table
     */
    public function __construct($info, $id, $table){
        parent::__construct($info, $id, $table);

        $this->fileID = parent::getSeeValue();
        $image = new File($this->fileID);
        $this->fileURL = $image->getAbsolutePath();
    }

    /**
     * show image on form
     * @param int|null $langID
     * @return string
     */
    public function getSeeValue($langID = null){
        if( $this->fileURL == null ){
            return '-';
        }else{
            return $this->getFile();
        }
    }

    /**
     * if there is han image: displayit and option to delete it
     * else: show file picker
     * @param null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        if( $this->fileURL == null ){
            return $this->inputType('file', $langID);
        }else{
            return '
                ' . $this->getFile() . '
                <a href="#" data-id="'. $this->fileID.'" data-name="'.$this->fieldName.'" class="btn btn-danger btn-xs delete-image" title="'.gettext('Eliminar').'" data-toggle="confirmation">
                    <i class="fa fa-trash"></i>
                </a>
            ';
        }
    }

    /**
     * only can save image if its set on form
     * @param int|null $langID
     * @return bool
     */
    public function canSave($langID = null){
        $postName = $this->getInputName($langID);
        if( isset($_FILES[$postName]) && !empty($_FILES[$postName]['tmp_name']) ){
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
        $postName = $this->getInputName($langID);
        $image = new File();
        $fileID = $image->save( $_FILES[$postName] );

        if( $fileID === false ){
            throw new Exception('Unable to save image <pre>' . print_r($_FILES[$this->fieldName], true) . '</pre>');
        }
        $this->fileID = $fileID;
        $this->fileURL = $image->getAbsolutePath();
        return $fileID;
    }

    /**
     * image tag with link to see it
     * @return string
     */
    protected function getFile(){
        return '
            <a href="'.$this->fileURL.'" class="pull-left pdf-file" target="_blank">
                <span class="fa fa-file-pdf-o"></span>
                <br/>
                '.basename($this->fileURL).'
            </a>
        ';
    }

}