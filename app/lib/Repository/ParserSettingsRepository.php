<?php


namespace ParseThisNews\Repository;


use ParseThisNews\Storage\iStorage;
use ParseThisNews\Storage\MySQLStorage;
use ParseThisNews\Model\ParserSettingsModel;

class ParserSettingsRepository implements iRepository
{
    /** @var iStorage|MySQLStorage  */
    private iStorage $storage;

    private const ENTITY_NAME = 'ptn_settings';

    public const FIELD_SOURCE = 'source';
    public const FIELD_LINK_SELECTOR = 'link_selector';
    public const FIELD_TITLE_SELECTOR = 'title_selector';
    public const FIELD_TEXT_SELECTOR = 'text_selector';
    public const FIELD_IMAGE_SELECTOR = 'image_selector';

    public function __construct()
    {
        $this->storage = new MySQLStorage();
    }

    public function get(array $filter): ?ParserSettingsModel
    {
        $result = $this->storage->get(self::ENTITY_NAME, $filter);
        if (empty($result)) {
            return null;
        }

        return (new ParserSettingsModel())
            ->setSource($result[self::FIELD_SOURCE])
            ->setImageSelector($result[self::FIELD_IMAGE_SELECTOR])
            ->setLinkSelector($result[self::FIELD_LINK_SELECTOR])
            ->setTitleSelector($result[self::FIELD_TITLE_SELECTOR])
            ->setTextSelector($result[self::FIELD_TEXT_SELECTOR]);
    }

    public function getAll(): array
    {
        $result = $this->storage->findAll(self::ENTITY_NAME);
        if (empty($result)) {
            return [];
        }

        return array_map(static function($element) {
            return (new ParserSettingsModel())
                ->setSource($element[self::FIELD_SOURCE])
                ->setImageSelector($element[self::FIELD_IMAGE_SELECTOR])
                ->setLinkSelector($element[self::FIELD_LINK_SELECTOR])
                ->setTitleSelector($element[self::FIELD_TITLE_SELECTOR])
                ->setTextSelector($element[self::FIELD_TEXT_SELECTOR]);
        }, $result);
    }

    /**
     * @param ParserSettingsModel $model
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
                self::FIELD_IMAGE_SELECTOR => $model->getImageSelector()
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