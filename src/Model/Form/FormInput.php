<?php

namespace Appacman\Model\Form;

use Core\Model\Model;
use PDO;

abstract class FormInput extends Model
{

    private static ?\Twig\Environment $twig = null;

    protected string $name = '';

    protected ?string $hint = '';

    protected string $fieldName = '';

    protected string|array|null $value = '';

    protected bool $isRequired = false;

    protected bool $isVisible = true;

    protected ?string $table = '';

    protected bool $onLangTable = false;

    protected bool|int $isMultiple = false;

    protected array $languages = array();

    protected int $type = PDO::PARAM_STR;

    protected string $fieldType = '';

    protected false|string $error = false;

    public function __construct(array $info, ?int $id, ?string $table = null)
    {
        parent::__construct();
        $this->id    = $id;
        $this->table = $table;

        $this->name       = $info['name'];
        $this->fieldName  = $info['field_name'];
        $this->value      = $info['value'];
        $this->isRequired = $info['required'];
        $this->fieldType  = $info['type'];
        if (array_key_exists('hint', $info)) {
            $this->hint = $info['hint'];
        }
        if (array_key_exists('type', $info) && in_array($info['type'], array('dynamic', 'selectMulti'))) {
            $this->isRequired = false;
        }
        if ($this->mysql->fieldExists($this->table . '_lang', $this->fieldName)) {
            $this->onLangTable = true;
        }
    }

    public function isMultiple($position = true): void
    {
        $this->isMultiple = $position;
    }

    public function setLanguages(array $languages): void
    {
        $this->languages = $languages;
    }

    public function setOnLangTable(bool $onLangTable = true): void
    {
        $this->onLangTable = $onLangTable;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(bool $isRequired): void
    {
        $this->isRequired = $isRequired;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPlaceholder(): string
    {
        return strip_tags($this->name);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->fieldType;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getHint(): string
    {
        return $this->hint;
    }

    public function getError(): bool|string
    {
        return $this->error;
    }

    public function setError(string $error): void
    {
        $this->error = $error;
    }

    public function isOnLangTable(): bool
    {
        return $this->onLangTable;
    }

    //*******************************************//
    //***************** L I S T *****************//
    //*******************************************//
    public function getListValue(): string
    {
        return $this->getSeeValue();
    }

    //*******************************************//
    //*** F O R M    N O N    E D I T A B L E ***//
    //*******************************************//
    /**
     * Value to show on form when user CANNOT edit
     * @return string
     */
    public function getInfoHTML(): string
    {
        $html = '';
        if ($this->onLangTable) {
            foreach ($this->languages as $language) {
                $value = $this->getSeeValue($language['id']);
                $html  .= $this->getFromRow($this->label($value), $language);
            }
        } else {
            $value = $this->getSeeValue();
            $html  .= $this->getFromRow($this->label($value));
        }

        return $html;
    }

    public function getSeeValue(?int $langID = null): mixed
    {
        return $this->getInputValue($langID);
    }

    //********************************************************//
    //******** F O R M    E D I T A B L E    P R I N T *******//
    //********************************************************//
    /**
     * Value to show on form when user CAN edit
     * @return string
     */
    public function getFormHTML(): string
    {
        $html = '';
        if ($this->onLangTable) {
            foreach ($this->languages as $language) {
                $html .= $this->getFromRow($this->getInputHTML($language['id']), $language);
            }
        } else {
            $html .= $this->getFromRow($this->getInputHTML());
        }

        return $html;
    }

    /**
     * hidden input
     * @return string
     */
    public function getHTML(): string
    {
        return '';
    }

    /**
     * Value to show on form when user CANNOT edit
     * @return string
     */
    public function getSeeHTML(): string
    {
        $html = '';
        if ($this->onLangTable) {
            foreach ($this->languages as $language) {
                $html .= $this->getFromRow($this->getSeeValue($language['id']), $language);
            }
        } else {
            $html .= $this->getFromRow($this->getSeeValue());
        }

        return $html;
    }

    /**
     * How the inout is displayed to de user in order to edit it
     *
     * @param int|null $langID
     *
     * @return string
     */
    abstract protected function getInputHTML(?int $langID = null): string;

    /**
     * Name of the input for post value
     *
     * @param ?int $langID
     * @param bool $withMultiple
     *
     * @return string
     */
    public function getInputName(?int $langID = null, bool $withMultiple = true): string
    {
        $fieldName = $this->fieldName;
        $multiple  = $this->isMultiple !== false && $withMultiple ? '[]' : '';
        if ($langID == null) {
            return $fieldName . $multiple;
        } else {
            return $fieldName . '_' . $langID . $multiple;
        }
    }

    protected function getInputValue(?int $langID = null): mixed
    {
        $postName = $this->getInputName($langID, false);
        if (array_key_exists($postName, $_POST)) {
            return $this->getPost($postName);
        } else {
            if (!empty($this->value)) {
                if ($langID == null && !is_array($this->value)) {
                    return $this->value;
                } else {
                    if ($langID == null) {
                        $keys = array_keys($this->value);
                        return $this->value[ $keys[0] ];
                    } else {
                        if (is_array($this->value)) {
                            if (array_key_exists('lang_' . $langID, $this->value)) {
                                return $this->value[ 'lang_' . $langID ];
                            } else {
                                return '';
                            }
                        }
                        return $this->value;
                    }
                }
            }
        }
        return '';
    }

    //********************************************************//
    //********* F O R M    E D I T A B L E    P O S T ********//
    //********************************************************//
    public function canSave(?int $langID = null): bool
    {
        return true;
    }

    protected function getPostValue(?int $langID = null): mixed
    {
        $postName = $this->getInputName($langID, false);
        $value    = $this->getPost($postName);

        if ($value) {
            return $value;
        } else {
            if ($this->isRequired()) {
                return '';
            }
        }
        return null;
    }

    protected function getPost($key, $default = ''): mixed
    {
        if (isset($_POST[ $key ])) {
            if ($this->isMultiple === false) {
                return $_POST[ $key ];
            } else {
                if ($_POST[ $key ] && isset($_POST[ $key ][ $this->isMultiple ])) {
                    return $_POST[ $key ][ $this->isMultiple ];
                } else {
                    return null;
                }
            }
        }
        return '';
    }

    public function getSaveValue(): ?array
    {
        // multi-language
        if ($this->onLangTable) {
            $values = array();
            foreach ($this->languages as $language) {
                if ($this->canSave($language['id'])) {
                    $this->error = $this->hasError($language['id']);
                    if (!$this->error) {
                        $values[ 'lang_' . $language['id'] ] = array(
                            $this->fieldName => array(
                                'value' => $this->getPostValue($language['id']),
                                'type'  => $this->type
                            )
                        );
                    }
                }
            }
            return $values;
        }

        // no language
        if ($this->canSave()) {
            $this->error = $this->hasError();
            if (!$this->error) {
                return array(
                    $this->fieldName => array('value' => $this->getPostValue(), 'type' => $this->type)
                );
            }
        }

        return null;
    }

    abstract protected function hasError(?int $langID = null): mixed;

    abstract public function save(int $itemID, ?int $langID = null): bool|string;

    //*******************************************//
    //*********** F O R M    U T I L S **********//
    //*******************************************//
    private function getFromRow(string $input, ?array $language = null): string
    {
        $name       = $span = '';
        $extraClass = '';
        if ($language != null) {
            $name       = $language['name'];
            $extraClass = ($language['id'] == null) ? '' : 'lang_' . $language['id'];
        }
        if (!empty($this->hint)) {
            $span .= '<span class="help-block">' . $this->hint . '</span>';
        }
        if ($this->error) {
            $extraClass = ' has-error';
            $span       .= '<span class="help-block"><i class="fa fa-times-circle-o"></i> ' . $this->error . '</span>';
        }

        return '
            <div class="form-horizontal ' . $extraClass . '">
                <div class="form-group">
                    <label class="col-sm-2 control-label">' . $name . '</label>
                    <div class="col-sm-10">
                        ' . $input . '
                        ' . $span . '
                    </div>
                </div>
            </div>
        ';
    }

    protected function label(string $value): string
    {
        return $this->renderTemplate('_label', array('value' => $value));
    }

    protected function inputType(string $type = 'text', ?int $langID = null, string $extra = ''): string
    {
        return $this->renderTemplate('_input', array(
            'type'        => $type,
            'postName'    => $this->getInputName($langID),
            'value'       => $this->getInputValue($langID),
            'placeholder' => $this->getPlaceholder(),
            'extra'       => $extra,
        ));
    }

    protected function renderTemplate(string $name, array $data): string
    {
        if (self::$twig === null) {
            $loader     = new \Twig\Loader\FilesystemLoader(APPACMAN_DIR . 'View/Form/');
            self::$twig = new \Twig\Environment($loader);
        }
        return self::$twig->render($name . '.twig', $data);
    }

    protected function getContentID(): int|bool
    {
        $lateralTable = str_replace('id_', '', $this->fieldName);
        $sql          = '
            SELECT id_appacman_content
            FROM appacman_content
            WHERE table_name = :table
            LIMIT 1
        ';
        $params       = array(
            'table' => array('value' => $lateralTable, 'type' => PDO::PARAM_STR)
        );
        $content      = $this->mysql->query($sql, $params);

        if (count($content)) {
            return $content[0]['id_appacman_content'];
        }
        return false;
    }

}