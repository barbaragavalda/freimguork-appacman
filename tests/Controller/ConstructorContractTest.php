<?php

namespace Appacman\Tests\Controller;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

/**
 * Every class under Appacman\Controller is ultimately built by Core\Container\Container
 * (see Core\Bootstrap::execute()), which only autowires - it never passes constructor
 * arguments explicitly. That means every constructor in this tree, at every level, must
 * resolve to exactly (Config, CacheManager, Session), in that order, or the container
 * will either fail to build the controller or silently drop a required dependency.
 */
class ConstructorContractTest extends TestCase
{

    private const EXPECTED_PARAMS = array(
        'Core\\Utils\\Config',
        'Core\\Controller\\CacheManager',
        'Core\\Utils\\Session',
    );

    public function testEveryControllerConstructorMatchesTheContainerContract(): void
    {
        $directory = __DIR__ . '/../../src/Controller/';
        $iterator  = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );

        $checked = array();
        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = substr($file->getPathname(), strlen($directory));
            $class    = 'Appacman\\Controller\\' . str_replace(
                array('/', '.php'),
                array('\\', ''),
                $relative
            );

            require_once $file->getPathname();

            $reflection = new ReflectionClass($class);
            $ctor       = $reflection->getConstructor();

            $this->assertNotNull($ctor, "$class has no constructor to autowire against");

            $types = array_map(
                fn($param) => $param->getType()?->getName(),
                $ctor->getParameters()
            );

            $this->assertSame(
                self::EXPECTED_PARAMS,
                $types,
                "$class's constructor (declared in {$ctor->getDeclaringClass()->getName()}) "
                . 'must resolve to (Config, CacheManager, Session)'
            );

            $checked[] = $class;
        }

        // sanity check that the scan actually found the controller tree, not an empty directory
        $this->assertGreaterThanOrEqual(20, count($checked));
    }

}
