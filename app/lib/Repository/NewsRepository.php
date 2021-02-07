<?php


namespace ParseThisNews\Repository;


use ParseThisNews\Model\News;
use ParseThisNews\Util\Repository;

class NewsRepository extends BaseRepository
{
    protected const ENTITY_NAME = 'ptn_news';

    public const FIELD_ID = 'id';
    public const FIELD_CODE = 'code';
    public const FIELD_SOURCE = 'source';
    public const FIELD_TITLE = 'title';
    public const FIELD_TEXT = 'text';
    public const FIELD_IMAGE = 'image';

    /**
     * @param array|null $filter
     * @return array [NewsModel]
     */
    public function get(?array $filter = null): array
    {
        $result = $this->storage->get(self::ENTITY_NAME, $filter);
        if (empty($result)) {
            return [];
        }

        return array_map(static function($element) {
            return (new News())
                ->setId($element[self::FIELD_ID])
                ->setCode($element[self::FIELD_CODE])
                ->setImage($element[self::FIELD_IMAGE])
                ->setSource($element[self::FIELD_SOURCE])
                ->setText($element[self::FIELD_TEXT])
                ->setTitle($element[self::FIELD_TITLE]);
        }, $result);
    }

    /**
     * @param News $model
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
}