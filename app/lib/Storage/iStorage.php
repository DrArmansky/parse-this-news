<?php


namespace ParseThisNews\Storage;


interface iStorage
{
    public function get(string $entityName, ?array $filter = []);

    public function create(string $entityName, array $data);

    public function update(string $entityName, $id, array $data);

    public function delete(string $entityName, ?array $condition = []);
}