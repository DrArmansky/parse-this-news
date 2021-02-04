<?php


namespace ParseThisNews\Util;


class Settings
{
    private static function getIniFilePath(): string
    {
        return dirname($_SERVER['DOCUMENT_ROOT']) . '/settings.ini';
    }

    public static function getSettings(?string $section = null): array
    {
        $settings = parse_ini_file(self::getIniFilePath(), $section !== null);
        if (empty($settings)) {
            return [];
        }
        return $section === null ? $settings : $settings[$section];
    }
}