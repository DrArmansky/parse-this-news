<?php


namespace ParseThisNewsApi\Controller;


use ParseThisNews\Model\ParserSettings;
use ParseThisNews\Repository\ParserSettingsRepository;
use ParseThisNews\Storage\MySQLStorage;
use ParseThisNewsApi\Formatter\SourceListFormatter;
use ParseThisNewsApi\Util\HTTPCodes;
use ParseThisNewsApi\Validator\UsingModelValidator;

class ParserController extends BaseController
{
    public function parseAction(): void
    {
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