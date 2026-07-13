<?php

namespace Appacman\Model\Form;

use Appacman\Model\ExtraUser;
use Appacman\Model\User;
use Core\Model\Utils\DateUtils;
use Core\Model\Utils\StringUtils;
use Core\Utils\Session;

class SelectDeepLink extends Select
{

    private ?int $profileID = null;

    protected function getOptions(?string $table = null, string $extraFields = ''): array
    {
        return parent::getOptions('appacman_push_deeplink', $extraFields);
    }

    public function getSeeValue(?int $langID = null): string
    {
        return $this->value;
    }

    protected function getInputHTML(?int $langID = null): string
    {
        $options = $this->getOptionsHTML($langID);

        $selects = $this->renderTemplate('select-deeplink-main', array(
            'fieldName'   => $this->fieldName,
            'placeholder' => _('Selecciona') . ' ' . $this->getPlaceholder(),
            'options'     => $options['main'],
        ));

        foreach ($options['secondary'] as $select) {
            $selects .= $select;
        }
        return $selects;
    }

    protected function getOptionsHTML(?int $langID): array
    {
        $mainOptions          = array();
        $secondaryOptionsHTML = array();
        $options              = $this->getOptions('appacman_push_deeplink', ', table_name, format');

        foreach ($options as $mainOption) {
            $format       = str_replace('{id}', '', $mainOption['format']);
            $selectedMain = str_starts_with($this->value, $format);

            $mainOptions[] = array(
                'id'       => $mainOption['id'],
                'value'    => $mainOption['id'] . '_' . $mainOption['format'],
                'name'     => $mainOption['name'],
                'selected' => $selectedMain,
            );

            if ($mainOption['table_name']) {
                $secondaryOptionsHTML[] = $this->getSelectOptions(
                    $mainOption['table_name'],
                    $selectedMain,
                    $mainOption['id']
                );
            }
        }

        return array(
            'main'      => $mainOptions,
            'secondary' => $secondaryOptionsHTML,
        );
    }

    private function getSelectOptions(string $tableName, bool $selectedMain, $mainOptionID): string
    {
        $secondaryOptions = $this->getOptions($tableName);

        if ($secondaryOptions !== null) {
            $value = '';
            $id    = explode('_', $this->value);
            if (isset($_POST[ $this->fieldName . '_' . $id[0] ])) {
                $value = $_POST[ $this->fieldName . '_' . $id[0] ];
            } else {
                if ($this->value) {
                    $value = explode('=', $this->value);
                    $value = $value[1];
                }
            }

            $optionData = array();
            foreach ($secondaryOptions as $secondaryOption) {
                $optionData[] = array(
                    'id'       => $secondaryOption['id'],
                    'name'     => $secondaryOption['name'],
                    'selected' => $selectedMain && $value == $secondaryOption['id'],
                );
            }

            return $this->renderTemplate('select-deeplink-secondary', array(
                'fieldName'   => $this->fieldName . '_' . $mainOptionID,
                'placeholder' => _('Selecciona') . ' ' . $this->getPlaceholder(),
                'options'     => $optionData,
            ));
        }

        return '';
    }

    protected function getPostValue(?int $langID = null): string
    {
        $inputName = $this->getInputName($langID);
        if (isset($_POST[ $inputName ])) {
            $mainSelect = explode('_', $_POST[ $inputName ], 2);
            if (isset($_POST[ $inputName . '_' . $mainSelect[0] ])) {
                return str_replace('{id}', $_POST[ $inputName . '_' . $mainSelect[0] ], $mainSelect[1]);
            }
            if (count($mainSelect) > 1) {
                return $mainSelect[1];
            }
        }
        return '';
    }

}