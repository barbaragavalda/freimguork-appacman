<?php

namespace Appacman\Model\Form;

use Core\Model\Encryptor\BlindIndex;
use PDO;

/**
 * Class EncryptedTwoWaySearchable
 * same as EncryptedTwoWay, but also persists a blind index (see
 * Core\Model\Encryptor\BlindIndex) in a companion `<field>_bidx` column -
 * that column must already exist on the content table. This lets the value
 * be looked up with an exact-match query instead of decrypting every row to
 * compare (see Appacman\Model\LoggedOut\UserForm::foundUser() for the case
 * this was built for: "does a user with this email already exist?").
 */
class EncryptedTwoWaySearchable extends EncryptedTwoWay
{

    public function getSaveValue(): ?array
    {
        $values = parent::getSaveValue();
        if ($values === null) {
            return null;
        }

        $bidxField = $this->fieldName . '_bidx';

        if ($this->onLangTable) {
            foreach ($this->languages as $language) {
                $langKey = 'lang_' . $language['id'];
                if (array_key_exists($langKey, $values)) {
                    $values[ $langKey ][ $bidxField ] = $this->bidxParam($language['id']);
                }
            }
        } else {
            $values[ $bidxField ] = $this->bidxParam();
        }

        return $values;
    }

    private function bidxParam(?int $langID = null): array
    {
        // FormInput::getPostValue() - the plaintext, before EncryptedTwoWay
        // encrypts it - needed so the index is computed over the same value
        $plainValue = (string) FormInput::getPostValue($langID);
        $bidx       = $plainValue === '' ? '' : BlindIndex::compute($plainValue, $this->fieldName);

        return array('value' => $bidx, 'type' => PDO::PARAM_STR);
    }

}
