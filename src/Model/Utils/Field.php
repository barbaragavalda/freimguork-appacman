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

    public function __construct($contentID, $contentTable){
        parent::__construct();

        $this->contentID = $contentID;
        $this->contentTable = $contentTable;

        $this->init();
    }

    public function getFieldsForList(){
        $names = array();
        foreach($this->fields as $field){
            $names[] = $field['field_name'];
        }

        return $names;
    }

    public function get(){
        return $this->fields;
    }

    private function init(){
        $sql = '
            SELECT af.field_name, afl.name, aft.name AS type
            FROM appacman_field AS af
            INNER JOIN appacman_field_lang AS afl ON afl.id_appacman_field = af.id_appacman_field AND afl.id_appacman_lang = :lang
            LEFT JOIN appacman_field_type AS aft ON aft.id_appacman_field_type = af.id_appacman_field_type
            WHERE af.id_appacman_content = :content_id AND af.show_on_list = 1
            ORDER BY af.order
        ';
        $params = array(
            'content_id'    => array('value' => $this->contentID,   'type' => \PDO::PARAM_INT),
            'lang'          => array('value' => $this->langID,      'type' => \PDO::PARAM_INT)
        );
        $this->fields = $this->mysql->query($sql, $params);

        foreach($this->fields as &$field){
            // field type
            $field['length'] = 0;
            if( !$field['type'] ){
                $typeInfo = $this->mysql->fieldType($this->contentTable, $field['field_name']);
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