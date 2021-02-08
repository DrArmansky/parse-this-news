<?php


namespace ParseThisNews\Util;


class Settings
{
    public static string $settingsPath = '';

    public static function getSettings(?string $section = null): array
    {
        $settings = parse_ini_file(self::$settingsPath, $section !== null);
        if (empty($settings)) {
            return [];
        }
        return $section === null ? $settings : $settings[$section];
    }
}