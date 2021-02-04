<?php


namespace ParseThisNews\Util;


class Repository
{
    public static function generateCodeFromRusString(string $string): string
    {
        $transliterated = \transliterator_transliterate('Russian-Latin/BGN', $string);
        $cleaned = strtolower(preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/u", '', $transliterated));

        return str_replace(' ', '-', $cleaned);
    }
}