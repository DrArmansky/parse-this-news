<?php


namespace ParseThisNews\Repository;


use ParseThisNews\Model\NewsModel;
use ParseThisNews\Storage\iStorage;
use ParseThisNews\Storage\MySQLStorage;
use ParseThisNews\Util\Repository;

class NewsRepository implements iRepository
{
    /** @var iStorage|MySQLStorage  */
    private iStorage $storage;

    private const ENTITY_NAME = 'ptn_news';

    public const FIELD_ID = 'id';
    public const FIELD_CODE = 'code';
    public const FIELD_SOURCE = 'source';
    public const FIELD_TITLE = 'title';
    public const FIELD_TEXT = 'text';
    public const FIELD_IMAGE = 'image';

    public function __construct()
    {
        $this->storage = new MySQLStorage();
    }

    public function get(array $filter): ?NewsModel
    {
        $result = $this->storage->get(self::ENTITY_NAME, $filter);
        if (empty($result)) {
            return null;
        }

        return (new NewsModel())
            ->setId($result[self::FIELD_ID])
            ->setCode($result[self::FIELD_CODE])
            ->setImage($result[self::FIELD_IMAGE])
            ->setSource($result[self::FIELD_SOURCE])
            ->setText($result[self::FIELD_TEXT])
            ->setTitle($result[self::FIELD_TITLE]);
    }

    /**
     * @return array [NewsModel]
     */
    public function getAll(): array
    {
        $result = $this->storage->findAll(self::ENTITY_NAME);
        if (empty($result)) {
            return [];
        }

        return array_map(static function($element) {
            return (new NewsModel())
                ->setId($element[self::FIELD_ID])
                ->setCode($element[self::FIELD_CODE])
                ->setImage($element[self::FIELD_IMAGE])
                ->setSource($element[self::FIELD_SOURCE])
                ->setText($element[self::FIELD_TEXT])
                ->setTitle($element[self::FIELD_TITLE]);
        }, $result);
    }

    /**
     * @param NewsModel $model
     * @return bool
     */
    public function add($model): bool
    {

        return $this->storage->create(
            self::ENTITY_NAME,
            [
                self::FIELD_CODE => Repository::generateCodeFromRusString($model->getTitle()),
                self::FIELD_SOURCE => $model->getSource(),
                self::FIELD_TITLE => $model->getTitle(),
                self::FIELD_TEXT => $model->getText(),
                self::FIELD_IMAGE => $model->getImage()
            ]
        );
    }



    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function clean(): bool
    {
        return $this->storage->deleteAll(self::ENTITY_NAME);
    }
}