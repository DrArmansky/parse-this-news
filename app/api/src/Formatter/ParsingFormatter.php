<?php


namespace ParseThisNewsApi\Formatter;


use ParseThisNews\Model\News;

class ParsingFormatter implements iFormatter
{
    /**
     * @param News[] $data
     * @return array
     */
    public function format($data): array
    {
       return [
           'result' => ['success' => !empty($data)],
           'error' => null
       ];
    }
}