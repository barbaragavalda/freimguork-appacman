<?php

namespace Appacman\Model\Utils;

use Core\Model\Model;

class Field extends Model {

    /**
     * @var int $contentID
     */
    private $contentID = 0;

    /**
     * @var string $contentTable
     */
    private $contentTable = '';

    /**
     * @var array $fields. Definition of the content fields
     */
    private $fields = array();

    public function __construct($contentTable, $contentID = null){
        parent::__construct();

        $this->contentID = $contentID;
        $this->contentTable = $contentTable;

        if( $this->contentID == null ){
            $sql = '
                SELECT id_appacman_content
                FROM appacman_content
                WHERE table_name = :table
            ';
            $params = array(
                'table' => array('value' => $this->contentTable, 'type' => \PDO::PARAM_STR),
            );
            $content = $this->mysql->query($sql, $params);
            if( count($content) ) $this->contentID = $content[0]['id_appacman_content'];
        }

        $this->init();
    }

    public function getFieldsForList(){
        $names = array();
        foreach($this->fields as $field){
            if( $field['show_on_list'] ){
                $names[] = $field;
            }
        }

        return $names;
    }

    public function getFieldsForExport(){
        $names = array();
        foreach($this->fields as $field){
            if( !in_array($field['type'], array('unmodifiable', 'encryptedOneWay', 'image', 'imageSeeOnly', 'genericFile', 'genericFileSeeOnly', 'dynamic')) ){
                $names[] = $field;
            }
        }

        return $names;
    }

    public function get(){
        return $this->fields;
    }

    private function init(){
        $sql = '
            SELECT af.id_appacman_field, af.field_name, af.show_on_list, af.show_on_breadcrumb, afl.name, afl.hint, aft.name AS type
            FROM appacman_field AS af
            INNER JOIN appacman_field_lang AS afl ON afl.id_appacman_field = af.id_appacman_field AND afl.id_appacman_lang = :lang
            LEFT JOIN appacman_field_type AS aft ON aft.id_appacman_field_type = af.id_appacman_field_type
            WHERE af.id_appacman_content = :content_id
            ORDER BY af.order
        ';
        $params = array(
            'content_id'    => array('value' => $this->contentID,   'type' => \PDO::PARAM_INT),
            'lang'          => array('value' => $this->langID,      'type' => \PDO::PARAM_INT)
        );
        $this->fields = $this->mysql->query($sql, $params);

        foreach($this->fields as &$field){
            $fieldDescription = $this->mysql->fieldDescription($this->contentTable, $field['field_name']);
            $field['length'] = 0;

            $required = false;
            if( !empty($fieldDescription) ){
                $required = $fieldDescription['required'];
            }
            $field['required'] = $required;

            // field type
            if( array_key_exists('type', $field) && !$field['type'] ){
                if( $fieldDescription ){
                    $typeInfo = $fieldDescription['type'];
                    $type = $typeInfo;
                    if( strpos($typeInfo, '(') !== false ){
                        $typeArray = explode('(', $typeInfo);
                        $type = $typeArray[0];
                        $field['length'] = intval( str_replace(')', '', $typeArray[1]) );
                    }
                    $field['type'] = $type;
                }
            }
        }
    }

}
