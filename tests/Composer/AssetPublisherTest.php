<?php

namespace Appacman\Tests\Composer;

use Appacman\Composer\AssetPublisher;
use PHPUnit\Framework\TestCase;

class AssetPublisherTest extends TestCase
{

    private string $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/appacman-asset-publisher-test-' . uniqid();
        mkdir($this->root, 0755, true);
    }

    protected function tearDown(): void
    {
        $this->removeRecursively($this->root);
    }

    public function testPublishesBothAssetDirsIntoWebFolder(): void
    {
        $this->makeFile('vendor/almasaeed2010/adminlte/css/adminlte.min.css', 'body{}');
        $this->makeFile('vendor/optisistem/freimguork-appacman/src/public/img/favicon.ico', 'icon');
        mkdir($this->root . '/web', 0755, true);

        $webFolder = AssetPublisher::resolveWebFolder($this->root);
        $this->assertSame('web', $webFolder);

        $messages = AssetPublisher::publishAssets($this->root, $webFolder);

        $this->assertCount(2, $messages);
        $this->assertFileExists($this->root . '/web/vendor/almasaeed2010/adminlte/css/adminlte.min.css');
        $this->assertSame('body{}', file_get_contents($this->root . '/web/vendor/almasaeed2010/adminlte/css/adminlte.min.css'));
        $this->assertFileExists($this->root . '/web/vendor/optisistem/freimguork-appacman/src/public/img/favicon.ico');
    }

    public function testFallsBackToPublicFolderWhenWebDoesNotExist(): void
    {
        mkdir($this->root . '/public', 0755, true);

        $this->assertSame('public', AssetPublisher::resolveWebFolder($this->root));
    }

    public function testPrefersWebOverPublicWhenBothExist(): void
    {
        mkdir($this->root . '/web', 0755, true);
        mkdir($this->root . '/public', 0755, true);

        $this->assertSame('web', AssetPublisher::resolveWebFolder($this->root));
    }

    public function testResolvesToNullWhenNeitherFolderExists(): void
    {
        $this->assertNull(AssetPublisher::resolveWebFolder($this->root));
    }

    public function testSkipsAnAssetDirThatDoesNotExistInVendor(): void
    {
        mkdir($this->root . '/web', 0755, true);
        // neither vendor/almasaeed2010/adminlte nor vendor/optisistem/freimguork-appacman/src/public exist

        $messages = AssetPublisher::publishAssets($this->root, 'web');

        $this->assertSame(array(), $messages);
    }

    public function testRepublishingReplacesStaleFilesFromAPreviousVersion(): void
    {
        $this->makeFile('vendor/almasaeed2010/adminlte/css/old.css', 'old');
        mkdir($this->root . '/web', 0755, true);
        AssetPublisher::publishAssets($this->root, 'web');

        $this->removeRecursively($this->root . '/vendor/almasaeed2010/adminlte');
        $this->makeFile('vendor/almasaeed2010/adminlte/css/new.css', 'new');
        AssetPublisher::publishAssets($this->root, 'web');

        $this->assertFileDoesNotExist($this->root . '/web/vendor/almasaeed2010/adminlte/css/old.css');
        $this->assertFileExists($this->root . '/web/vendor/almasaeed2010/adminlte/css/new.css');
    }

    private function makeFile(string $relativePath, string $content): void
    {
        $path = $this->root . '/' . $relativePath;
        mkdir(dirname($path), 0755, true);
        file_put_contents($path, $content);
    }

    private function removeRecursively(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($directory);
    }

}
