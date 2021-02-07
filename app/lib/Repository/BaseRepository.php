<?php


namespace ParseThisNews\Repository;


use ParseThisNews\Storage\iStorage;
use ParseThisNews\Storage\MySQLStorage;

abstract class BaseRepository implements iRepository
{
    protected const ENTITY_NAME = '';

    protected iStorage $storage;

    public function __construct(iStorage $storage) {
        $this->storage = $storage;
    }

    abstract public function add($model);

    abstract public function get(array $filter);

    public function delete(array $condition): bool
    {
        if (empty($condition)) {
            throw new \InvalidArgumentException('Empty condition');
        }

        return $this->delete($condition);
    }

    public function clean(): bool
    {
        return $this->storage->delete(self::ENTITY_NAME);
    }
}