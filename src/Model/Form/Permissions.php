<?php

namespace Appacman\Model\Form;

use Core\Utils\Session;
use PDO;

class Permissions extends FormInput
{

    private ?array $contents = null;

    private bool $isSuperAdmin     = false;
    private ?int $profileID        = null;
    private ?int $referenceProfile = null;

    protected function getInputHTML(?int $langID = null): string
    {
        // check if permissions can be edited
        $this->initUserProfile();
        $this->initRefereneProfile();
        $this->isSuperAdmin = in_array($this->profileID, $this->getConfig('superadmin_profiles'));
        $unmodifiable       = in_array($this->id, $this->getConfig('admin_profiles'));
        if (!$this->isSuperAdmin && $unmodifiable) {
            return _('No se pueden modificar los permisos de los administradores.');
        }

        // create permissions form
        $this->initContents();

        $form = '';
        foreach ($this->contents as $content) {
            $form .= '
                <div class="form-horizontal lang_2">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">'
                . $content['name']
                . '</label>
                        <div class="col-sm-10">
                            <select name="'
                . $this->fieldName
                . '_'
                . $content['id']
                . '[]"  class="form-control select2 select2-hidden-accessible" multiple="" data-placeholder="'
                . _('Selecciona')
                . ' '
                . $this->getPlaceholder()
                . '" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                '
                . $this->getOptionsHTML($content['id'])
                . '
                            </select>
                        </div>
                    </div>
                </div>
            ';
        }
        return $form;
    }

    public function hasError(?int $langID = null): bool
    {
        return false;
    }

    public function canSave(?int $langID = null): bool
    {
        return false;
    }

    public function save(int $itemID, ?int $langID = null): bool
    {
        // delete all
        $sql    = '
            DELETE FROM appacman_user_profile_permission
            WHERE id_appacman_user_profile = :id_appacman_user_profile
        ';
        $params = array(
            'id_appacman_user_profile' => array('value' => $this->id, 'type' => PDO::PARAM_INT)
        );
        $this->mysql->query($sql, $params);

        // create insert query
        if ($this->mysql->getState()) {
            $this->initContents();

            $values = array();
            foreach ($this->contents as $content) {
                $contentID                                     = $content['id'];
                $params[ 'id_appacman_content_' . $contentID ] = array(
                    'value' => $contentID,
                    'type'  => PDO::PARAM_INT
                );
                $postName                                      = $this->fieldName . '_' . $contentID;
                if (isset($_POST[ $postName ])) {
                    foreach ($_POST[ $postName ] as $index => $id) {
                        $values[]                                    = '(:id_appacman_user_profile, :id_appacman_content_'
                            . $contentID
                            . ', :id_'
                            . $contentID
                            . '_'
                            . $index
                            . ')';
                        $params[ 'id_' . $contentID . '_' . $index ] = array('value' => $id, 'type' => PDO::PARAM_INT);
                    }
                }
            }

            $sql = '
                INSERT INTO appacman_user_profile_permission (id_appacman_user_profile, id_appacman_content, id_appacman_user_permission) 
                VALUES ' . implode(',', $values) . '
            ';
            $this->mysql->query($sql, $params);
            if ($this->mysql->getState()) {
                return false;
            }
        }
        return true;
    }

    private function initUserProfile(): void
    {
        if ($this->profileID == null) {
            $sql     = '
                SELECT id_appacman_user_profile AS id
                FROM appacman_user
                WHERE id_appacman_user = :id
            ';
            $session = Session::getInstance();
            $params  = array(
                'id' => array('value' => $session->get('user_id'), 'type' => PDO::PARAM_INT)
            );
            $profile = $this->mysql->query($sql, $params);

            if (count($profile)) {
                $this->profileID = $profile[0]['id'];
            }
        }
    }

    private function initRefereneProfile(): void
    {
        $profiles = $this->getConfig('reference_profile');
        if (count($profiles)) {
            $this->referenceProfile = $profiles[0];
        }
    }

    private function getConfig($name): string|array
    {
        $sql    = '
            SELECT value
            FROM appacman_config
            WHERE name = :name
        ';
        $params = array(
            'name' => array('value' => $name, 'type' => PDO::PARAM_STR)
        );
        $admins = $this->mysql->query($sql, $params);
        if (count($admins)) {
            return explode(',', $admins[0]['value']);
        }
        return array();
    }

    private function initContents(): void
    {
        if ($this->contents == null) {
            $sql            = '
                SELECT ac.id_appacman_content AS id, ac.icon, acl.name
                FROM appacman_content AS ac 
                INNER JOIN appacman_content_lang AS acl ON ac.id_appacman_content = acl.id_appacman_content AND acl.id_appacman_lang = :lang
                INNER JOIN appacman_block AS ab USING(id_appacman_block)
                WHERE ac.icon IS NOT NULL
                ORDER BY ab.order ASC, ac.order ASC
            ';
            $params         = array(
                'lang' => array('value' => $this->langID, 'type' => PDO::PARAM_INT)
            );
            $this->contents = $this->mysql->query($sql, $params);
        }
    }

    private function getOptionsHTML(int $contentID): string
    {
        $sql    = '
            SELECT aupl.id_appacman_user_permission AS id, aupl.name
            FROM appacman_user_permission AS aup
            INNER JOIN appacman_user_permission_lang AS aupl ON aupl.id_appacman_user_permission = aup.id_appacman_user_permission AND aupl.id_appacman_lang = :lang
        ';
        $params = array(
            'lang' => array('value' => $this->langID, 'type' => PDO::PARAM_INT)
        );
        if (!$this->isSuperAdmin) {
            $sql                        .= '
                WHERE aupl.id_appacman_user_permission IN(
                        SELECT id_appacman_user_permission
                        FROM appacman_user_profile_permission
                        WHERE id_appacman_user_profile = :referenceProfile AND id_appacman_content = :id
                    ) 
                    AND aup.code NOT IN ("firebase", "own", "export")
            ';
            $params['referenceProfile'] = array('value' => $this->referenceProfile, 'type' => PDO::PARAM_INT);
            $params['id']               = array('value' => $contentID, 'type' => PDO::PARAM_INT);
        }
        $permission = $this->mysql->query($sql, $params);

        $optionsHTML = '<option></option>';
        $values      = $this->loadValues($contentID);

        foreach ($permission as $option) {
            $selected    = in_array($option['id'], $values) !== false ? 'selected' : '';
            $name        = $option['name'];
            $optionsHTML .= '<option value="' . $option['id'] . '" ' . $selected . '>' . $name . '</option>';
        }

        return $optionsHTML;
    }

    private function loadValues(int $contentID): array
    {
        if (isset($_POST['save'])) {
            return $_POST[ $this->fieldName . '_' . $contentID ] ?? array();
        }

        $sql    = '
            SELECT aupp.id_appacman_user_permission AS id
            FROM appacman_user_profile_permission AS aupp
            WHERE aupp.id_appacman_user_profile = :id AND aupp.id_appacman_content = :content
        ';
        $params = array(
            'id'      => array('value' => $this->id, 'type' => PDO::PARAM_INT),
            'content' => array('value' => $contentID, 'type' => PDO::PARAM_INT),
        );
        $values = $this->mysql->query($sql, $params);
        return array_column($values, 'id');
    }

}