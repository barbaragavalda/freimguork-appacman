<?php

namespace Appacman\Tests\Model\Utils;

use Appacman\Model\Utils\Permissions;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Permissions::__construct() -> parent::__construct() pulls in a real DB connection via
 * Core\Model\Model's defaults, and load() queries it directly - these tests build via
 * reflection (no constructor call) and set only the already-loaded $permissions state
 * that hasPermission()/getContentPermissions() actually read.
 */
class PermissionsTest extends TestCase
{

    private function make(array $loadedPermissions): Permissions
    {
        $reflection = new ReflectionClass(Permissions::class);
        $instance   = $reflection->newInstanceWithoutConstructor();
        $reflection->getProperty('permissions')->setValue($instance, $loadedPermissions);
        // getContentPermissions() iterates this in the same fixed order the real
        // constructor builds it in - replicated here since we skip that constructor
        $reflection->getProperty('permissionsCodes')->setValue($instance, array(
            Permissions::CREATE,
            Permissions::DELETE,
            Permissions::EDIT,
            Permissions::SEE,
            Permissions::EXPORT,
            Permissions::LOCK,
            Permissions::OWN,
            Permissions::DUPLICATE,
            Permissions::SEND_CHANGES,
            Permissions::LOG_OUT,
            Permissions::GENERATE_INVOICE,
        ));
        return $instance;
    }

    public function testHasPermissionFindsAGrantedCode(): void
    {
        $permissions = $this->make(array(
            'c5' => array(
                array('code' => Permissions::EDIT, 'name' => 'Editar'),
                array('code' => Permissions::DELETE, 'name' => 'Eliminar'),
            ),
        ));

        $result = $permissions->hasPermission(5, Permissions::EDIT);

        $this->assertSame(array('code' => Permissions::EDIT, 'name' => 'Editar'), $result);
    }

    public function testHasPermissionReturnsFalseForAnUngrantedCode(): void
    {
        $permissions = $this->make(array(
            'c5' => array(array('code' => Permissions::EDIT, 'name' => 'Editar')),
        ));

        $this->assertFalse($permissions->hasPermission(5, Permissions::DELETE));
    }

    public function testHasPermissionReturnsFalseForAContentWithNoPermissionsAtAll(): void
    {
        $permissions = $this->make(array());

        $this->assertFalse($permissions->hasPermission(999, Permissions::SEE));
    }

    public function testGetContentPermissionsReturnsOnlyTheGrantedOnesInDeclarationOrder(): void
    {
        $permissions = $this->make(array(
            'c5' => array(
                array('code' => Permissions::DELETE, 'name' => 'Eliminar'),
                array('code' => Permissions::CREATE, 'name' => 'Crear'),
            ),
        ));

        $result = $permissions->getContentPermissions(5);

        // order follows Permissions::$permissionsCodes (CREATE before DELETE),
        // not the order permissions were loaded in
        $this->assertSame(array(
            array('code' => Permissions::CREATE, 'name' => 'Crear'),
            array('code' => Permissions::DELETE, 'name' => 'Eliminar'),
        ), $result);
    }

    public function testGetContentPermissionsIsEmptyWhenNothingIsGranted(): void
    {
        $permissions = $this->make(array());

        $this->assertSame(array(), $permissions->getContentPermissions(5));
    }

}
