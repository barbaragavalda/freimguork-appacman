<?php

namespace Appacman\Model\Form;

class ImageSeeOnly extends Image
{

    protected function getInputHTML(?int $langID = null): string
    {
        if ($this->fileURL == null) {
            return $this->inputType('file', $langID);
        } else {
            return $this->getImage();
        }
    }

    protected function getImage(): string
    {
        return $this->renderTemplate('image-download', array(
            'linkFile'      => $this->getLinkFile(),
            'fileURL'       => $this->fileURL,
            'downloadLabel' => _('Descargar'),
        ));
    }

    public function canSave(?int $langID = null): bool
    {
        return false;
    }

    public function hasError(?int $langID = null): bool
    {
        return false;
    }

}