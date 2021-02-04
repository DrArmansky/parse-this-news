<?php


namespace ParseThisNews\Storage;


interface iStorage
{
    public function findAll(string $entityName);

    public function get(string $entityName, array $filer);

    public function create(string $entityName, array $data);

    public function update(string $entityName, $id, array $data);

    public function delete(string $entityName, $id);

    public function deleteAll(string $entityName);
}