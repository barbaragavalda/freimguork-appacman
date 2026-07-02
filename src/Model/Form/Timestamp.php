<?php

namespace Appacman\Model\Form;

use Core\Model\Utils\DateUtils;
use DateInterval;

class Timestamp extends FormInput
{

    public function getListValue(): string
    {
        return parent::getSeeValue();
    }

    protected function getInputHTML(?int $langID = null): string
    {
        $value = self::getSeeValue($langID);
        if (!$value && $this->isRequired) {
            $value       = date(DateUtils::FORMAT_TIMESTAMP_DB);
            $this->value = $value;
        }
        return $this->label($value) . $this->inputType('hidden', $langID);
    }

    /**
     * @throws \DateMalformedStringException
     */
    protected function getPostValue(?int $langID = null): string
    {
        $value = parent::getPostValue($langID);
        if (!$value && $this->value) {
            $value = $this->value;
        }
        if (!$value && $this->isRequired) {
            $date = new \DateTime(date(DateUtils::FORMAT_TIMESTAMP_DB));
            $date->add(new DateInterval('PT5M'));
            $value = $date->format('Y-m-d H:i:s');
        }
        $this->value = $value;
        return $value;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function hasError(?int $langID = null): bool|string
    {
        $postValue = $this->getPostValue($langID);
        if ($postValue == null && $this->isRequired) {
            return _('Campo obligatorio.');
        }
        return false;
    }

    public function save(int $itemID, ?int $langID = null): bool
    {
        return false;
    }

}