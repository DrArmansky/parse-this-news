<?php


namespace ParseThisNews\Util;


class Template
{
    public static function render(string $path, ?array $templateData = []): string
    {
        \extract($templateData, EXTR_OVERWRITE);

        \ob_start();
        try {
            if (file_exists($path)) {
                require_once $path;
            }
        } catch (\Throwable $ex) {
            return "Template ${$path} error";
        } finally {
            return \ob_get_clean();
        }
    }
}