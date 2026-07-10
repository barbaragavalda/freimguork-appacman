<?php

namespace Appacman\Composer;

use Composer\Script\Event;

/**
 * Copies (never symlinks) appacman's own public assets and the AdminLTE theme's assets into the
 * consuming app's web-served folder, preserving the same "vendor/..." relative path so
 * Appacman\Controller\AppacmanController's adminDomain/vendorDomain URLs keep resolving unchanged.
 *
 * A copy, not a symlink, because production deploys here are FTP-only: whatever a normal
 * "composer install, then FTP the web folder up" workflow produces has to already contain real
 * files, not links that FTP/the target filesystem may not preserve.
 */
class AssetPublisher
{

    private const ASSET_PATHS = array(
        'vendor/almasaeed2010/adminlte',
        'vendor/optisistem/freimguork-appacman/src/public',
    );

    public static function publish(Event $event): void
    {
        $io = $event->getIO();

        $webFolder = self::resolveWebFolder(getcwd());
        if ($webFolder === null) {
            $io->writeError('<warning>[appacman] No web/ or public/ folder found - skipping asset publish.</warning>');
            return;
        }

        foreach (self::publishAssets(getcwd(), $webFolder) as $message) {
            $io->write('<info>[appacman] ' . $message . '</info>');
        }
    }

    /**
     * @return array<string> one message per asset actually copied, for the caller to report
     */
    public static function publishAssets(string $projectRoot, string $webFolder): array
    {
        $messages = array();

        foreach (self::ASSET_PATHS as $relativePath) {
            $source = $projectRoot . '/' . $relativePath;
            if (!is_dir($source)) {
                continue;
            }

            $target = $projectRoot . '/' . $webFolder . '/' . $relativePath;
            self::removeDirectory($target);
            self::copyDirectory($source, $target);

            $messages[] = "published $relativePath to $webFolder/$relativePath";
        }

        return $messages;
    }

    /**
     * Prefers "web" (the new deploy convention) over "public" (the old one) when both somehow
     * exist; returns null if neither does, so the caller can skip without failing the install.
     */
    public static function resolveWebFolder(string $projectRoot): ?string
    {
        if (is_dir($projectRoot . '/web')) {
            return 'web';
        }
        if (is_dir($projectRoot . '/public')) {
            return 'public';
        }

        return null;
    }

    private static function copyDirectory(string $source, string $target): void
    {
        mkdir($target, 0755, true);

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            $destination = $target . '/' . substr($item->getPathname(), strlen($source) + 1);
            if ($item->isDir()) {
                mkdir($destination, 0755, true);
            } else {
                copy($item->getPathname(), $destination);
            }
        }
    }

    private static function removeDirectory(string $directory): void
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
