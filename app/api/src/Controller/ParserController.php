<?php


namespace ParseThisNewsApi\Controller;


use ParseThisNews\Model\News;
use ParseThisNews\Model\ParserSettings;
use ParseThisNews\Parser\Service\NewsParser;
use ParseThisNews\Repository\NewsRepository;
use ParseThisNews\Repository\ParserSettingsRepository;
use ParseThisNews\Storage\MySQLStorage;
use ParseThisNewsApi\Exception\ConflictException;
use ParseThisNewsApi\Formatter\ParsingFormatter;
use ParseThisNewsApi\Formatter\SourceListFormatter;
use ParseThisNewsApi\Util\HTTPCodes;
use ParseThisNewsApi\Validator\ParsingValidator;
use ParseThisNewsApi\Validator\UsingModelValidator;

class ParserController extends BaseController
{
    public function parseAction(): void
    {
        $this->validateRequest(new ParsingValidator());
        $source = $this->request->get('source');

        $this->checkSource($source);
        $settings = $this->getSettingsForSource($source);

        $news = [];
        try {
            $news = (new NewsParser($settings))->parse();
            $this->saveParsedNews($news);
        } catch (\Throwable $exception) {
            $this->sendError($exception);
        }

        $this->sendResponse(HTTPCodes::OK, $this->formatResponseData($news, new ParsingFormatter()));
    }

    /**
     * @param News[] $news
     */
    protected function saveParsedNews(array $news): void
    {
        if (empty($news)) {
            return;
        }

        array_walk($news, static function($newsItem) {
            (new NewsRepository(new MySQLStorage()))->add($newsItem);
        });
    }

    protected function getSettingsForSource(string $source): ParserSettings
    {
        $settingsFromStorage = (new ParserSettingsRepository(new MySQLStorage()))->get([
            ParserSettingsRepository::FIELD_SOURCE => $source
       ]);

        if (empty($settingsFromStorage)) {
            $this->sendError(
                new \RuntimeException("Settings for {$source} was not defined", 500)
            );
        }

        return reset($settingsFromStorage);
    }

    protected function checkSource(string $source): void
    {
        $parsedNews = (new NewsRepository(new MySQLStorage()))->get([NewsRepository::FIELD_SOURCE => $source]);
        if (!empty($parsedNews)) {
            $this->sendError(new ConflictException("Source {$source} is already parsed"));
        }
    }

    public function getSourceList(): void
    {
        $parserSettings = (new ParserSettingsRepository(new MySQLStorage()))->get();
        $formatter = new SourceListFormatter();

        if (empty($parserSettings)) {
            $this->sendResponse(200, $formatter->format([]));
        }

        $sourceList = $this->formatResponseData($parserSettings, $formatter);
        $this->sendResponse(200, $sourceList);
    }

    public function saveSettingsAction(): void
    {
        $this->validateRequest(new UsingModelValidator(ParserSettings::class));

        $parseSettingsModel = $this->createParseSettingsModelByRequest();
        $result = (new ParserSettingsRepository(new MySQLStorage()))->add($parseSettingsModel);
        if (!$result) {
            $this->sendError(
                new \RuntimeException(
                    'Add to repository error',
                    HTTPCodes::INTERNAL_ERROR
                )
            );
        }

        $this->sendResponse(HTTPCodes::OK);
    }

    protected function createParseSettingsModelByRequest(): ParserSettings
    {
        return (new ParserSettings())
            ->setSource($this->request->get('source'))
            ->setNewsLimit($this->request->get('newsLimit'))
            ->setTextSelector($this->request->get('textSelector'))
            ->setTitleSelector($this->request->get('titleSelector'))
            ->setLinkSelector($this->request->get('linkSelector'))
            ->setImageSelector($this->request->get('imageSelector'));
    }
}