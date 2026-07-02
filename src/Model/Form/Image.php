<?php

namespace Appacman\Model\Form;

class Image extends GenericFile
{

    private array $allowedExtensions = array('png', 'jpg', 'jpeg', 'gif', 'svg', 'webp');

    protected function getLinkFile(): string
    {
        $type = mime_content_type($this->filePath);
        if (str_starts_with($type, 'image/')) {
            return '
                <a href="' . $this->fileURL . '" class="pull-left media-object" target="_blank">
                    <img src="' . $this->fileURL . '" />
                </a>
            ';
        }
        if (str_starts_with($type, 'video/')) {
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

    public function hasError(?int $langID = null): bool|string
    {
        $error = parent::hasError($langID);
        $file  = $this->getPostFile($langID);
        if (!$error && !empty($file['tmp_name'])) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $this->allowedExtensions)) {
                return str_replace(
                    '%types%',
                    implode(', ', $this->allowedExtensions),
                    _('La imagen debe ser de tipo %types%')
                );
            }
        }
        return $error;
    }

}