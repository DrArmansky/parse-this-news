<?php

namespace ParseThisNews\Parser;


interface iParser
{
    public function parse(string $resource): void;
}
