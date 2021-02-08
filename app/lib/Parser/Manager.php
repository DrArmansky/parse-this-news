<?php


namespace ParseThisNews\Parser;


use DiDom\Exceptions\InvalidSelectorException;
use GuzzleHttp\Exception\GuzzleException;
use ParseThisNews\Model\News;
use ParseThisNews\Model\ParserSettings;
use ParseThisNews\Parser\Service\NewsParser;
use ParseThisNews\Repository\NewsRepository;
use ParseThisNews\Repository\ParserSettingsRepository;
use ParseThisNews\Storage\MySQLStorage;
use ParseThisNews\Util\Settings;

class Manager
{
    /**
     * @param string $resource
     * @return News[]
     *
     * @throws InvalidSelectorException
     * @throws GuzzleException
     */
    public function parseNewsFromResource(string $resource): array
    {
        $this->setDefaultSettings($resource);
        $settings = $this->getSettingsForSource($resource);
        if ($settings === null) {
            throw new \RuntimeException("Parse settings for {$resource} are not defined");
        }

        //TODO::can be empty
        return (new NewsParser($settings))->parse();
    }

    protected function getSettingsForSource(string $source): ?ParserSettings
    {
        $settingRepository = new ParserSettingsRepository(new MySQLStorage());
        $settingsFromStorage = $settingRepository->get([$settingRepository::FIELD_SOURCE => $source]);

        return reset($settingsFromStorage) ?: null;
    }

    //TODO::move to settings controller
    protected function setDefaultSettings(string $source): bool
    {
        $defaultSettings = Settings::getSettings($source);
        $repository = new ParserSettingsRepository(new MySQLStorage());
        return $repository->add(
            (new ParserSettings())
                ->setSource($source)
                ->setTitleSelector($defaultSettings['title_selector'])
                ->setLinkSelector($defaultSettings['link_selector'])
                ->setTextSelector($defaultSettings['text_selector'])
                ->setImageSelector($defaultSettings['image_selector'])
                ->setNewsLimit($defaultSettings['news_limit'])
        );
    }
}