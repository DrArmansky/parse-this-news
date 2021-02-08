<?php


namespace ParseThisNews\Repository;


use ParseThisNews\Model\ParserSettings;

class ParserSettingsRepository extends BaseRepository
{
    protected const ENTITY_NAME = 'ptn_settings';

    public const FIELD_SOURCE = 'source';
    public const FIELD_LIMIT = 'news_limit';
    public const FIELD_LINK_SELECTOR = 'link_selector';
    public const FIELD_TITLE_SELECTOR = 'title_selector';
    public const FIELD_TEXT_SELECTOR = 'text_selector';
    public const FIELD_IMAGE_SELECTOR = 'image_selector';

    /**
     * @param array $filter
     * @return ParserSettings[]
     */
    public function get(array $filter): array
    {
        $result = $this->storage->get(self::ENTITY_NAME, $filter);
        if (empty($result)) {
            return [];
        }

        return array_map(static function($element) {
            return (new ParserSettings())
                ->setSource($element[self::FIELD_SOURCE])
                ->setImageSelector($element[self::FIELD_IMAGE_SELECTOR])
                ->setLinkSelector($element[self::FIELD_LINK_SELECTOR])
                ->setTitleSelector($element[self::FIELD_TITLE_SELECTOR])
                ->setTextSelector($element[self::FIELD_TEXT_SELECTOR])
                ->setNewsLimit($element[self::FIELD_LIMIT]);
        }, $result);
    }

    /**
     * @param ParserSettings $model
     * @return bool
     */
    public function add($model): bool
    {
        return $this->storage->create(
            self::ENTITY_NAME,
            [
                self::FIELD_SOURCE => $model->getSource(),
                self::FIELD_TITLE_SELECTOR => $model->getTitleSelector(),
                self::FIELD_LINK_SELECTOR => $model->getLinkSelector(),
                self::FIELD_TEXT_SELECTOR => $model->getTextSelector(),
                self::FIELD_IMAGE_SELECTOR => $model->getImageSelector(),
                self::FIELD_LIMIT => $model->getNewsLimit()
            ]
        );
    }
}