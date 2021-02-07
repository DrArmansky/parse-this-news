<?php


namespace ParseThisNews\Parser\ResultStorage;

use ParseThisNews\Model\News;
use ParseThisNews\Repository\iRepository;
use ParseThisNews\Repository\NewsRepository;
use ParseThisNews\Storage\MySQLStorage;

class NewsResultStorage implements iResultStorage
{
    private iRepository $repository;

    public function __construct()
    {
        $this->repository = new NewsRepository(new MySQLStorage());
    }

    public function save($result): void
    {
        $model = (new News())
            ->setSource($result['SOURCE'])
            ->setTitle($result['TITLE'])
            ->setText($result['TEXT'])
            ->setImage($result['IMAGE_SRC']);

        $this->repository->add($model);
    }
}