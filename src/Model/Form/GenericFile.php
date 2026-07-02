<?php

namespace Appacman\Model\Form;

use Core\Model\File;
use Core\Utils\Exception;

class GenericFile extends FormInput
{

    private int            $postID   = -1;
    private array|int|null $postFile = -1;

    protected ?int $fileID = null;

    protected ?string $fileURL  = null;
    protected ?string $filePath = null;

    private ?int $fieldID;

    public function __construct(array $info, ?string $id, ?string $table)
    {
        $id = (int)$id;
        parent::__construct($info, $id, $table);

        $this->fieldID = $info['id_appacman_field'];
        
        $this->fileID = (int)parent::getSeeValue();
        if (!is_array($this->fileID)) {
            $this->initFile();
        }
    }

    public function initFile(?int $lang = null): void
    {
        $this->fileID = (int)parent::getSeeValue($lang);

        $image          = new File($this->fileID);
        $this->fileURL  = $image->getAbsolutePath();
        $this->filePath = $image->getRelativePath();
        if (!$this->fileURL) {
            $this->fileID = null;
        }
    }

    public function getPostFile(?int $langID = null): ?array
    {
        $postName = $this->getInputName($langID, false);
        if ($this->postFile == -1 || $this->postFile == null) {
            $this->postFile = null;
            if (isset($_FILES[ $postName ])) {
                if ($this->isMultiple === false) {
                    $this->postFile = $_FILES[ $postName ];
                } else {
                    $file = array();
                    foreach ($_FILES[ $postName ] as $key => $value) {
                        if (count($value) > $this->isMultiple) {
                            $file[ $key ] = $value[ $this->isMultiple ];
                        }
                    }
                    $this->postFile = $file;
                }
            }
        }
        return $this->postFile;
    }

    public function getSeeValue(?int $langID = null): string
    {
        if ($this->fileURL == null) {
            return '-';
        } else {
            return $this->getLinkFile();
        }
    }

    protected function getInputHTML(?int $langID = null): string
    {
        $this->initFile($langID);
        if ($this->fileURL == null) {
            return $this->inputType('file', $langID) . $this->inputType('hidden', $langID);
        } else {
            $fieldName = parent::getInputName($langID);
            return '
                '
                . $this->getLinkFile()
                . '
                <div class="pull-left file-actions">
                    <a href="#" data-id="'
                . $this->fileID
                . '" data-item="'
                . $this->id
                . '" data-name="'
                . $fieldName
                . '" data-field="'
                . $this->fieldName
                . '" data-table="'
                . $this->table
                . '" class="btn btn-danger btn-xs delete-file" title="'
                . _('Eliminar')
                . '" data-toggle="confirmation">
                        <i class="fa fa-trash"></i>
                    </a>
                    <a href="'
                . $this->fileURL
                . '" class="btn bg-purple btn-xs" title="'
                . _('Descargar')
                . '" download target="_blank">
                        <i class="fa fa-download"></i>
                    </a>
                    '
                . $this->inputType('hidden', $langID)
                . '
                    <div style="display: none">'
                . $this->inputType('file', $langID)
                . '</div>
                </div>
            ';
        }
    }

    /**
     * @throws \Core\Utils\Exception
     */
    protected function getPostValue(?int $langID = null): ?string
    {
        // already uploaded
        if ($this->isMultiple !== false && $this->fileID) {
            if ($this->postID == -1) {
                $this->postID = $this->fileID;
            }
            return $this->fileID;
        }

        // upload file
        $this->fileID   = false;
        $this->postID   = -1;
        $this->postFile = -1;
        $file           = $this->getPostFile($langID);
        if ($file && !empty($file['tmp_name'])) {
            $image  = new File();
            $fileID = $image->save($file, $this->fieldID);
            if ($fileID === false) {
                throw new Exception('Unable to save image <pre>' . print_r($file, true) . '</pre>');
            }

            $this->fileID  = $fileID;
            $this->postID  = $fileID;
            $this->fileURL = $image->getAbsolutePath();

            $postName           = $this->getInputName($langID);
            $this->value        = $this->fileID;
            $_POST[ $postName ] = $this->fileID;
            return $fileID;
        }

        // current image
        $value = parent::getInputValue($langID);
        if (!empty($value)) {
            return $value;
        }

        //no value
        return null;
    }

    protected function getLinkFile(): string
    {
        return '
            <a href="' . $this->fileURL . '" class="pull-left pdf-file" target="_blank">
                <span class="fa fa-file-pdf-o"></span>
                <br/>
                ' . basename($this->fileURL) . '
            </a>
        ';
    }

    public function hasError(?int $langID = null): bool|string
    {
        $value = parent::getInputValue($langID);
        $file  = $this->getPostFile($langID);
        if (empty($value) && empty($file['tmp_name']) && $this->isRequired) {
            return _('Campo obligatorio.');
        }
        return false;
    }

    public function save(int $itemID, ?int $langID = null): bool|string
    {
        return false;
    }

}