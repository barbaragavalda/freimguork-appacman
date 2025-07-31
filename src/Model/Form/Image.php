<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\StringUtils;

class Image extends GenericFile {

    private $allowedExtensions = array('png', 'jpg', 'jpeg', 'gif', 'svg', 'webp');

    /**
     * image tag with link to see it
     * @return string
     */
    protected function getLinkFile()
    {
        $type = mime_content_type($this->filePath);
        if (StringUtils::startsWidth($type, 'image/')) {
            return '
                <a href="' . $this->fileURL . '" class="pull-left media-object" target="_blank">
                    <img src="' . $this->fileURL . '" />
                </a>
            ';
        }
        if (StringUtils::startsWidth($type, 'video/')) {
            return '
                <a href="' . $this->fileURL . '" class="pull-left media-object" target="_blank">
                    <video controls>
                      <source src="' . $this->fileURL . '">
                    </video>
                </a>
            ';
        }
        return parent::getLinkFile();
    }

    /**
     * Check file extension
     * @param null $langID
     * @return false|string
     */
    public function hasError($langID = null){
        $error = parent::hasError($langID);
        $file = $this->getPostFile($langID);
        if( !$error && !empty($file['tmp_name']) ){
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if( !in_array($extension, $this->allowedExtensions) ){
                return str_replace('%types%', implode(', ', $this->allowedExtensions), gettext('La imagen debe ser de tipo %types%'));
            }
        }
        return $error;
    }

}