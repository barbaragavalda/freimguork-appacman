<?php

namespace Appacman\Tests\Model\Utils;

use Appacman\Model\Utils\Field;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Field::__construct()/init() query the DB directly - these tests build via reflection
 * (no constructor call) and set only the already-loaded $fields state that
 * getFieldsForList()/getFieldsForExport() actually read.
 */
class FieldTest extends TestCase
{

    private function make(array $fields): Field
    {
        $reflection = new ReflectionClass(Field::class);
        $instance   = $reflection->newInstanceWithoutConstructor();
        $reflection->getProperty('fields')->setValue($instance, $fields);
        return $instance;
    }

    public function testGetFieldsForListKeepsOnlyFieldsFlaggedShowOnList(): void
    {
        $field = $this->make(array(
            array('field_name' => 'title', 'show_on_list' => true, 'type' => 'varchar'),
            array('field_name' => 'notes', 'show_on_list' => false, 'type' => 'varchar'),
        ));

        $result = $field->getFieldsForList();

        $this->assertCount(1, $result);
        $this->assertSame('title', $result[0]['field_name']);
    }

    public function testGetFieldsForExportExcludesNonExportableTypes(): void
    {
        $field = $this->make(array(
            array('field_name' => 'title', 'show_on_list' => true, 'type' => 'varchar'),
            array('field_name' => 'photo', 'show_on_list' => true, 'type' => 'image'),
            array('field_name' => 'legacy_id', 'show_on_list' => true, 'type' => 'unmodifiable'),
            array('field_name' => 'ssn', 'show_on_list' => true, 'type' => 'encryptedOneWay'),
            array('field_name' => 'attachment', 'show_on_list' => true, 'type' => 'genericFile'),
            array('field_name' => 'extra', 'show_on_list' => true, 'type' => 'dynamic'),
        ));

        $result = $field->getFieldsForExport();

        $this->assertCount(1, $result);
        $this->assertSame('title', $result[0]['field_name']);
    }

    public function testGetReturnsEveryFieldRegardlessOfFlags(): void
    {
        $fields = array(
            array('field_name' => 'title', 'show_on_list' => false, 'type' => 'image'),
        );
        $field  = $this->make($fields);

        $this->assertSame($fields, $field->get());
    }

}
