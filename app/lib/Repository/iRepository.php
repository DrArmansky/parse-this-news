<?php


namespace ParseThisNews\Repository;


interface iRepository
{
    public function get(array $filter);

    public function add($model);

    public function delete(array $condition);

    public function clean();
}