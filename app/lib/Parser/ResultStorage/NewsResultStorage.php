<?php


namespace ParseThisNews\Parser\ResultStorage;

use ParseThisNews\Model\NewsModel;
use ParseThisNews\Repository\iRepository;
use ParseThisNews\Repository\NewsRepository;

class NewsResultStorage implements iResultStorage
{
    private iRepository $repository;

    public function __construct()
    {
        $this->repository = new NewsRepository();
    }

    public function save($result): void
    {
        $model = (new NewsModel())
            ->setSource($result['SOURCE'])
            ->setTitle($result['TITLE'])
            ->setText($result['TEXT'])
            ->setImage($result['IMAGE_SRC']);

        $this->repository->add($model);
    }
}