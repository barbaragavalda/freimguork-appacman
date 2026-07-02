<?php

namespace Appacman\Model\Form;

class DateTime extends Timestamp
{

    const string EMPTY_DATETIME = '0000-00-00 00:00:00';

    public function getValue(): string
    {
        return $this->checkEmpty($this->value);
    }

    protected function getInputHTML(?int $langID = null): string
    {
        $postName  = $this->getInputName($langID);
        $postValue = $this->checkEmpty($this->getPostValue($langID));
        return '
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control datetimepicker" id="'
            . $postName
            . '" name="'
            . $postName
            . '" placeholder="'
            . $this->getPlaceholder()
            . '" value="'
            . $postValue
            . '">
            </div>
        ';
    }

    public function hasError(?int $langID = null): bool|string
    {
        $value = $this->checkEmpty(parent::getPostValue($langID));
        if (!empty($value)
            && preg_match(
                '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])$/',
                $value
            ) == false) {
            return str_replace(
                '%format%',
                'yyyy-mm-dd hh:mm:ss',
                _('Comprueba que sea una fecha correcta con el formato %format%.')
            );
        }
    }

    private function checkEmpty(string $value): ?string
    {
        return ($value == self::EMPTY_DATETIME) ? '' : $value;
    }

}