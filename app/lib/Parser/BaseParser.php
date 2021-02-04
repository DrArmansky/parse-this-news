<?php


namespace ParseThisNews\Parser;


use ParseThisNews\Parser\ResultStorage\iResultStorage;

abstract class BaseParser implements iParser
{
    private iResultStorage $resultStorage;

    public function __construct(iResultStorage $resultStorage)
    {
        $this->resultStorage = $resultStorage;
    }

    abstract public function parse(string $resource): void;

    public function putResultToStorage($result): void
    {
        $this->resultStorage->save($result);
    }
}