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

        $selects = '
            <select name="'
            . $this->fieldName
            . '" id="deeplink" class="form-control select2 select2-hidden-accessible" data-placeholder="'
            . _('Selecciona')
            . ' '
            . $this->getPlaceholder()
            . '" style="width: 100%;" tabindex="-1" aria-hidden="true">
                '
            . $options['main']
            . '
            </select>
        ';

        foreach ($options['secondary'] as $select) {
            $selects .= $select;
        }
        return $selects;
    }

    protected function getOptionsHTML(?int $langID): array
    {
        $mainHTML             = '';
        $secondaryOptionsHTML = array();
        $options              = $this->getOptions('appacman_push_deeplink', ', table_name, format');

        $mainHTML .= '<option value=""></option>';
        foreach ($options as $mainOption) {
            $format = str_replace('{id}', '', $mainOption['format']);

            $selectedMain = (str_starts_with($this->value, $format)) ? 'selected' : '';
            $mainHTML     .= '<option value="'
                . $mainOption['id']
                . '_'
                . $mainOption['format']
                . '" '
                . $selectedMain
                . ' data-id="'
                . $mainOption['id']
                . '" >'
                . $mainOption['name']
                . '</option>';

            if ($mainOption['table_name']) {
                $secondaryHTML          = '<div style="margin-top: 10px">' . $this->getSelectOptions(
                        $mainOption['table_name'],
                        $selectedMain,
                        $mainOption['id']
                    ) . '</div>';
                $secondaryOptionsHTML[] = $secondaryHTML;
            }
        }

        return array(
            'main'      => $mainHTML,
            'secondary' => $secondaryOptionsHTML,
        );
    }

    private function getSelectOptions(string $tableName, $selectedMain, $mainOptionID): string
    {
        $secondaryOptions = $this->getOptions($tableName);

        if ($secondaryOptions !== null) {
            $secondaryHTML = '<select name="'
                . $this->fieldName
                . '_'
                . $mainOptionID
                . '" class="deepLinkID form-control select2 select2-hidden-accessible" data-placeholder="'
                . _('Selecciona')
                . ' '
                . $this->getPlaceholder()
                . '" style="width: 100%;" tabindex="-1" aria-hidden="true">';
            if (count($secondaryOptions)) {
                $id    = explode('_', $this->value);
                $value = '';
                if (isset($_POST[ $this->fieldName . '_' . $id[0] ])) {
                    $value = $_POST[ $this->fieldName . '_' . $id[0] ];
                } else {
                    if ($this->value) {
                        $value = explode('=', $this->value);
                        $value = $value[1];
                    }
                }
                foreach ($secondaryOptions as $secondaryOption) {
                    $selectedSecondary = $selectedMain && $value == $secondaryOption['id'] ? 'selected' : '';
                    $secondaryHTML     .= '<option value="'
                        . $secondaryOption['id']
                        . '" '
                        . $selectedSecondary
                        . '>'
                        . $secondaryOption['name']
                        . '</option>';
                }
            } else {
                $secondaryHTML .= '<option disabled selected>-</option>';
            }
            $secondaryHTML .= '</select>';
            return $secondaryHTML;
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