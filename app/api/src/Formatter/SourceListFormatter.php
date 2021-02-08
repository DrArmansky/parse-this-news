<?php


namespace ParseThisNewsApi\Formatter;


class SourceListFormatter implements iFormatter
{

    /**
     * @param array $data
     * @return array
     */
    public function format($data): array
    {
        $sourcesInfo = [];
        foreach ($data['ALL_SOURCES'] as $source) {
            $sourcesInfo[] = [
                'NAME' => $source,
                'IS_PARSED' => in_array($source, $data['PARSED_SOURCES'], true)
            ];
        }

        return [
            'result' => $sourcesInfo,
            'error' => null
        ];
    }
}