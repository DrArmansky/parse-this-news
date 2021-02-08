<?php

use ParseThisNews\Util\Settings;

require_once dirname($_SERVER['DOCUMENT_ROOT'], 2) . '/vendor/autoload.php';

Settings::$settingsPath = dirname($_SERVER['DOCUMENT_ROOT'], 2) . '/settings.ini';