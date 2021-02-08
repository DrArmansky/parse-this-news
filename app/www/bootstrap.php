<?php

use ParseThisNews\Util\Settings;

require_once '../vendor/autoload.php';

Settings::$settingsPath = dirname($_SERVER['DOCUMENT_ROOT']) . '/settings.ini';