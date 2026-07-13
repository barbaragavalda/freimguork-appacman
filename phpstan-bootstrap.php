<?php

// APPACMAN_DIR is always defined by freimguork-core (Core\Utils\Config) before any
// appacman code runs (this package is never run standalone) - declared here purely
// so PHPStan knows it exists, this file is never included at runtime.
define('APPACMAN_DIR', __DIR__ . '/src/');
