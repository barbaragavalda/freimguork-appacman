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
        return '
            ' . $this->getLinkFile() . '
            <a href="' . $this->fileURL . '" class="btn bg-purple btn-xs" title="' . _('Descargar') . '" download target="_blank">
                <i class="fa fa-download"></i>
            </a>
        ';
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