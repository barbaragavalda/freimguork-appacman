<?php

namespace Appacman\Model;

use Core\Model\File;
use Core\Model\Utils\StringUtils;
use Core\Utils\Exception;
use PDO;

class Item extends Page
{

    protected array $form = array();

    private array $post     = array();
    private array $postLang = array();
    private bool  $error    = false;

    public function __construct($id, $table)
    {
        parent::__construct($id);
        $this->table = $table;
    }

    public function getName(): string
    {
        if (count($this->info)) {
            return StringUtils::truncateHtml($this->name, 35);
        }
        return _('Crear nuevo item');
    }

    public function getForm(): array
    {
        return $this->form;
    }

    public function setForm($form): void
    {
        $this->form = $form;
    }

    public function hasLang(): bool
    {
        return $this->mysql->tableExists($this->table . '_lang');
    }

    public function getError(): bool
    {
        return $this->error;
    }

    /**
     * get the formulari for that item
     *
     * @param array $languages
     *
     * @return array
     */
    public function get(array $languages = array()): array
    {
        $this->initFields($this->table);
        $fields = $this->fields->get();

        $this->form = array();
        foreach ($fields as $field) {
            $input = $this->getInputClass($field);
            $input->setLanguages($languages);
            $this->form[] = $input;

            // page title
            if ($field['show_on_breadcrumb'] && empty($this->name)) {
                $this->name = strip_tags($input->getSeeValue($this->langID));
            }
            unset($field['show_on_breadcrumb']);
            unset($field['show_on_list']);
        }
        return $this->form;
    }

    /**
     * check if this item exists
     * @return bool
     */
    public function exists(): bool
    {
        $info = $this->getInfo($this->table);

        if (count($info)) {
            $this->info = $info[0];

            // lang table
            $tableLang = $this->table . '_lang';
            if ($this->mysql->tableExists($tableLang)) {
                $infoLang = $this->getInfo($tableLang);
                foreach ($infoLang as $lang) {
                    $langID = $lang['id_appacman_lang'];
                    foreach ($lang as $field => $value) {
                        if (!array_key_exists($field, $this->info)) {
                            $this->info[ $field ] = array();
                        }
                        if (is_array($this->info[ $field ])) {
                            $this->info[ $field ][ 'lang_' . $langID ] = $value;
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    private function getInfo(string $table): array
    {
        $sql    = "
            SELECT *, t.id_$table AS id
            FROM $table AS t
            WHERE t.id_$this->table = :id
        ";
        $params = array(
            'id' => array('value' => $this->id, 'type' => PDO::PARAM_INT)
        );
        return $this->mysql->query($sql, $params);
    }

    /**
     * prepare post
     */
    public function preparePost(): void
    {
        // prepare post
        $this->post     = array();
        $this->postLang = array();
        $this->error    = false;
        foreach ($this->form as $input) {
            $value    = $input->getSaveValue();
            $hasError = $input->getError();
            if (!$this->error && $hasError) {
                $this->error = $hasError;
            }
            if ($value != null) {
                if ($input->isOnLangTable()) {
                    $this->postLang = array_merge_recursive($this->postLang, $value);
                } else {
                    $this->post = array_merge_recursive($this->post, $value);
                }
            }
        }
    }

    /**
     * saves item
     * @return bool success
     */
    public function save(): bool
    {
        $canCommit = false;
        if (!$this->mysql->inTransaction()) {
            $canCommit = true;
            $this->mysql->beginTransaction();
        }

        $error = false;
        try {
            if (!$this->error) {
                if ($this->id) {
                    // update
                    $error = $this->update($this->post);
                    if (!$error) {
                        foreach ($this->postLang as $lang => $post) {
                            $langID = str_replace('lang_', '', $lang);
                            $error  = $this->update($post, $langID);
                            if ($error) {
                                break;
                            }
                        }
                    }
                } else {
                    //insert
                    $error = $this->insert($this->post);
                    if (!$error) {
                        foreach ($this->postLang as $lang => $post) {
                            $langID = str_replace('lang_', '', $lang);
                            $error  = $this->insert($post, $langID);
                            if ($error) {
                                break;
                            }
                        }
                    }
                }
            }

            if (!$error) {
                // save extra info of some inputs
                foreach ($this->form as $input) {
                    if ($input->isOnLangTable()) {
                        foreach ($this->postLang as $lang => $post) {
                            $langID = str_replace('lang_', '', $lang);
                            $error  = $input->save($this->id, $langID);
                            if ($error) {
                                break;
                            }
                        }
                    } else {
                        $error = $input->save($this->id);
                    }
                    if ($error) {
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            $error = true;
        }

        if ($error) {
            if ($canCommit) {
                $this->mysql->rollBack();
            }
            return false;
        } else {
            if ($canCommit) {
                $this->mysql->commit();
            }
            return true;
        }
    }

    private function update(array $params, ?int $langID = null): bool
    {
        $fields = $this->getFields($params);
        if ($fields) {
            $tableName    = $this->table;
            $whereLang    = '';
            $paramsUpdate = $params;
            if ($langID != null) {
                $tableName               = $this->table . '_lang';
                $whereLang               = 'AND id_appacman_lang = :lang_id';
                $paramsUpdate['lang_id'] = array('value' => $langID, 'type' => PDO::PARAM_INT);
            }

            if ($this->existsRow($langID)) {
                $sql                = '
                    UPDATE ' . $tableName . '
                    SET ' . $fields . '    
                    WHERE id_' . $this->table . ' = :id ' . $whereLang . '
                ';
                $paramsUpdate['id'] = array('value' => $this->id, 'type' => PDO::PARAM_INT);
                $this->mysql->query($sql, $paramsUpdate);
                return !$this->mysql->getState();
            } else {
                return $this->insert($params, $langID);
            }
        }
        return false;
    }

    private function existsRow($langID = null): bool
    {
        $tableName = $this->table;
        $whereLang = '';
        $params    = array(
            'id' => array('value' => $this->id, 'type' => PDO::PARAM_INT)
        );
        if ($langID != null) {
            $tableName         = $this->table . '_lang';
            $whereLang         = 'AND id_appacman_lang = :lang_id';
            $params['lang_id'] = array('value' => $langID, 'type' => PDO::PARAM_INT);
        }

        $sql    = "
            SELECT *
            FROM $tableName
            WHERE id_$this->table = :id $whereLang
        ";
        $exists = $this->mysql->query($sql, $params);

        if (count($exists)) {
            return true;
        }
        return false;
    }

    private function insert(array $params, ?int $langID = null): bool
    {
        $tableName   = $this->table;
        $extraFields = '';

        if (count($params)) {
            if ($langID != null) {
                $tableName                      = $this->table . '_lang';
                $params[ 'id_' . $this->table ] = array('value' => $this->id, 'type' => PDO::PARAM_INT);
                $params['id_appacman_lang']     = array('value' => $langID, 'type' => PDO::PARAM_INT);
            }
        }

        $table = $this->table;
        if ($langID != null) {
            $table = $this->table . '_lang';
        }
        $id                       = $this->mysql->getMaxId($table);
        $params[ 'id_' . $table ] = array('value' => $id, 'type' => PDO::PARAM_INT);
        $fields                   = $this->getFields($params);

        $sql = "
            INSERT INTO $tableName
            SET $fields$extraFields
        ";
        $this->mysql->query($sql, $params);
        if ($langID == null) {
            $this->id = $this->mysql->lastInsertId();
        }
        return !$this->mysql->getState();
    }

    public function block(int $state): bool
    {
        $sql    = "
            UPDATE $this->table
            SET is_locked = :state
            WHERE id_$this->table = :id
        ";
        $params = array(
            'state' => array('value' => $state, 'type' => PDO::PARAM_BOOL),
            'id'    => array('value' => $this->id, 'type' => PDO::PARAM_INT),
        );
        $this->mysql->query($sql, $params);
        return $this->mysql->getState();
    }

    private function getFields(array $params): string
    {
        $set = array();
        foreach ($params as $field => $param) {
            $set[] = "`$field` = :$field";
        }
        return implode(', ', $set);
    }

    /**
     * delete item
     * @return bool
     */
    public function delete(): bool
    {
        $this->deleteFiles();
        return $this->deleteFromDatabase();
    }

    private function deleteFiles(): void
    {
        $this->get();
        $files = array();
        foreach ($this->form as $input) {
            if (is_a($input, 'Appacman\Model\Form\GenericFile')) {
                $fileID = $input->getValue();
                if (is_array($fileID)) {
                    $files = array_merge($files, array_values($fileID));
                } else {
                    $files = array_merge($files, array($fileID));
                }
            }
        }

        $files = array_filter($files);
        foreach ($files as $fileID) {
            $file = new File($fileID);
            $file->deleteFromFileTable();
            $file->deleteFromDisk();
        }
    }

    private function deleteFromDatabase(): bool
    {
        $success = false;
        $this->mysql->beginTransaction();

        // delete no language
        $sql    = '
            DELETE FROM ' . $this->table . '
            WHERE id_' . $this->table . ' = :id
        ';
        $params = array(
            'id' => array('value' => $this->id, 'type' => PDO::PARAM_INT)
        );
        $this->mysql->query($sql, $params);

        // delete multi-language
        if ($this->mysql->getState()) {
            $tableLang = $this->table . '_lang';
            if ($this->mysql->tableExists($tableLang)) {
                $sql = '
                    DELETE FROM ' . $tableLang . '
                    WHERE id_' . $this->table . ' = :id
                ';
                $this->mysql->query($sql, $params);
                if ($this->mysql->getState()) {
                    $success = true;
                }
            } else {
                $success = true;
            }
        }

        if ($success) {
            $this->mysql->commit();
            return true;
        } else {
            $this->mysql->rollback();
            return false;
        }
    }

}