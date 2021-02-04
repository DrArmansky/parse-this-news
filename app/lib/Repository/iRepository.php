<?php


namespace ParseThisNews\Repository;


interface iRepository
{
    public function get(array $filter);

    public function getAll();

    public function add($model);

    public function delete($id);

    public function clean();
}