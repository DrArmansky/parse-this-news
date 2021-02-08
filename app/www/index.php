<?php

use ParseThisNews\Util\Template;

require_once 'bootstrap.php';

$header = Template::render($_SERVER['DOCUMENT_ROOT'] . '/header.php');
$footer = Template::render($_SERVER['DOCUMENT_ROOT'] . '/footer.php');

ob_start();
require 'routing.php';
$content = ob_get_clean();

echo $header . $content . $footer;
