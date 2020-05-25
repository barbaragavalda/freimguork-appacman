<?php

namespace Appacman\Model\Form;

use Core\Model\File;
use Core\Utils\Exception;

class GenericFile extends FormInput {

    private $postID = -1;
    private $postFile = -1;

    /**
     * @var int $id. Image id
     */
    protected $fileID = null;

    /**
     * @var string $fileURL. Image Path
     */
    protected $fileURL = null;

    private $fieldID = null;

    /**
     * initialize image and save its URL
     * @param $info
     * @param $id
     * @param string|null $table
     */
    public function __construct($info, $id, $table){
        parent::__construct($info, $id, $table);

        $this->fieldID = $info['id_appacman_field'];

        $this->fileID = parent::getSeeValue();
        if( !is_array($this->fileID) ){
            $this->initFile();
        }
    }

    public function initFile($lang = null){
        $this->fileID = parent::getSeeValue($lang);

        $image = new File($this->fileID);
        $this->fileURL = $image->getAbsolutePath();
        if( !$this->fileURL ) $this->fileID = null;
    }

    public function getPostFile($langID = null){
        $postName = $this->getInputName($langID, false);
        if( $this->postFile == -1 || $this->postFile == null ){
            $this->postFile = null;
            if( isset($_FILES[$postName]) ){
                if( $this->isMultiple === false ){
                    $this->postFile = $_FILES[$postName];
                }else{
                    $file = array();
                    foreach($_FILES[$postName] as $key => $value){
                        if( count($value) > $this->isMultiple ){
                            $file[$key] = $value[$this->isMultiple];
                        }
                    }
                    $this->postFile = $file;
                }
            }
        }
        return $this->postFile;
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
            return $this->getLinkFile();
        }
    }

    /**
     * if there is han image: displayit and option to delete it
     * else: show file picker
     * @param null $langID
     * @return string
     */
    protected function getInputHTML($langID = null){
        $this->initFile($langID);
        if( $this->fileURL == null ){
            return $this->inputType('hidden', $langID) . $this->inputType('file', $langID);
        }else{
            $fieldName = parent::getInputName($langID);
            return '
                ' . $this->getLinkFile() . '
                <div class="pull-left file-actions">
                    <a href="#" data-id="'. $this->fileID.'" data-item="'. $this->id.'" data-name="'.$fieldName.'" data-field="'.$this->fieldName.'" data-table="'.$this->table.'" class="btn btn-danger btn-xs delete-file" title="'.gettext('Eliminar').'" data-toggle="confirmation">
                        <i class="fa fa-trash"></i>
                    </a>
                    <a href="'.$this->fileURL.'" class="btn bg-purple btn-xs" title="'.gettext('Descargar').'" download target="_blank">
                        <i class="fa fa-download"></i>
                    </a>
                    ' . $this->inputType('hidden', $langID) . '
                    <div style="display: none">' . $this->inputType('file', $langID) . '</div>
                </div>
            ';
        }
    }

    /**
     * saves image on disk and return its database id
     * @param int|null $langID
     * @return false|int
     * @throws Exception
     */
    protected function getPostValue($langID = null){
        // already uploaded
        if( $this->fileID ){
            if( $this->isMultiple !== false && $this->postID == -1 ){
                $this->postID = $this->fileID;
            }

            return $this->fileID;
        }

        // upload file
        $file = $this->getPostFile($langID);
        if( $file && !empty($file['tmp_name']) ){
            $image = new File();
            $fileID = $image->save( $file );
            $resize = $this->getResize();
            if( $resize ){
                $image->resize($resize);
            }

            if( $fileID === false ){
                throw new Exception('Unable to save image <pre>' . print_r($file, true) . '</pre>');
            }
            $this->fileID = $fileID;
            $this->postID = $fileID;
            $this->fileURL = $image->getAbsolutePath();

            $postName = $this->getInputName($langID);
            $this->value = $this->fileID;
            $_POST[$postName] = $this->fileID;
            return $fileID;
        }

        // current image
        $value = parent::getInputValue($langID);
        if( !empty($value) ) return $value;

        //no value
        return null;
    }

    /**
     * Resize description of han image
     * @return false|array
     */
    private function getResize(){
        $sql = '
            SELECT afr.width, afr.height, afr.suffix
            FROM appacman_file_resize AS afr
            WHERE afr.id_appacman_field = :field_id
        ';
        $params = array(
            'field_id' => array('value' => $this->fieldID, 'type' => \PDO::PARAM_STR)
        );
        $resize = $this->mysql->query($sql, $params);

        if( count($resize) ){
            return $resize;
        }
        return false;
    }

    /**
     * image tag with link to see it
     * @return string
     */
    protected function getLinkFile(){
        return '
            <a href="'.$this->fileURL.'" class="pull-left pdf-file" target="_blank">
                <span class="fa fa-file-pdf-o"></span>
                <br/>
                '.basename($this->fileURL).'
            </a>
        ';
    }

    /**
     * Check if its required
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        $value = parent::getInputValue($langID);
        $file = $this->getPostFile($langID);
        if( empty($value) && empty($file['tmp_name']) && $this->isRequired ){
            return gettext('Campo obligatorio.');
        }
        return false;
    }

    public function save($itemID, $langID = null){
        return false;
    }

}