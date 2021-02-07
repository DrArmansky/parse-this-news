<?php

require_once '../vendor/autoload.php';

$header = \ParseThisNews\Util\Template::render($_SERVER['DOCUMENT_ROOT'] . '/header.php');
$footer = \ParseThisNews\Util\Template::render($_SERVER['DOCUMENT_ROOT'] . '/footer.php');

ob_start();
require 'routing.php';
$content = ob_get_clean();

echo $header . $content . $footer;
