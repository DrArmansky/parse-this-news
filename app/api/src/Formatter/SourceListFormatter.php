<?php


namespace ParseThisNewsApi\Formatter;


use ParseThisNews\Model\ParserSettings;

class SourceListFormatter implements iFormatter
{

    /**
     * @param ParserSettings[] $data
     * @return array
     */
    public function format($data): array
    {
        $sourceList = array_map(static function($settings) {
            return $settings->getSource();
        }, $data);

        return [
            'result' => ['sources' => $sourceList],
            'error' => null
        ];
    }
}