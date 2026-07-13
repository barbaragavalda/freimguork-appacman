<?php

// normally defined by a consuming app's public/index.php before anything else runs;
// tests bypass that entry point, so define harmless placeholders here instead.
define('DIR_ROOT', __DIR__ . '/../');
define('IS_DEV', true);

// normally defined by Core\Utils\Config (see freimguork-core/src/Utils/Config.php) once
// a consuming app boots; FormInput::renderTemplate() reads it directly to locate this
// package's own View/Form/ templates, so tests need it too.
define('APPACMAN_DIR', __DIR__ . '/../src/');

require __DIR__ . '/../vendor/autoload.php';
