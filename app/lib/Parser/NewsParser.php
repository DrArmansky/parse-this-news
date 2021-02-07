<?php

namespace ParseThisNews\Parser;

use DiDom\Document;
use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use ParseThisNews\Model\ParserSettings;
use ParseThisNews\Repository\ParserSettingsRepository;
use ParseThisNews\Util\Settings;


class NewsParser extends BaseParser
{
    private ?ParserSettings $settings;

    /**
     * @param string $resource
     *
     * @throws GuzzleException
     * @throws InvalidSelectorException
     */
    public function parse(string $resource): void
    {
        //Just for dev
        if (!$this->setDefaultSettings($resource)) {
            throw new \RuntimeException('Settings problem');
        }

        $this->settings = $this->getSettingsForSource($resource);
        $html = $this->getHtmlFromSource($resource);
        $links = $this->getLinksFromDocument(new Document($html));
        $this->parseContentByLinks($links);
    }

    protected function getSettingsForSource(string $source): ?ParserSettings
    {
        $settingRepository = new ParserSettingsRepository();
        return $settingRepository->get([$settingRepository::FIELD_SOURCE => $source]);
    }

    protected function setDefaultSettings(string $source): bool
    {
        $defaultSettings = Settings::getSettings($source);
        $repository = new ParserSettingsRepository();
        return $repository->add(
            (new ParserSettings())
                ->setSource($source)
                ->setTitleSelector($defaultSettings['title_selector'])
                ->setLinkSelector($defaultSettings['link_selector'])
                ->setTextSelector($defaultSettings['text_selector'])
                ->setImageSelector($defaultSettings['image_selector'])
        );
    }

    /**
     * @param string $source
     * @return string
     *
     * @throws GuzzleException
     */
    protected function getHtmlFromSource(string $source): string
    {
        $client = new Client();
        $response = $client->get($source);

        return $response->getBody()->getContents();
    }

    /**
     * @param Document $document
     * @return array
     *
     * @throws InvalidSelectorException
     */
    protected function getLinksFromDocument(Document $document): array
    {
        if ($this->settings === null) {
            throw new \RuntimeException('Parsing settings are not defined');
        }

        $elements = $document->find($this->settings->getLinkSelector());

        if (empty($elements)) {
            return [];
        }

        $result = [];

        /** @var Element $element */
        foreach ($elements as $element) {
            $link = $element->getAttribute('href');
            if (empty($link)) {
                continue;
            }
            $result[] = $link;
        }

        return $result;
    }

    /**
     * @param array $links
     * @param int|null $limit
     * @throws GuzzleException
     * @throws InvalidSelectorException
     */
    protected function parseContentByLinks(array $links, ?int $limit = 15): void
    {
        if ($this->settings === null) {
            throw new \RuntimeException('Parsing settings are not defined');
        }

        $counter = 0;
        foreach ($links as $link) {
            if ($counter === $limit) {
                break;
            }

            $html = $this->getHtmlFromSource($link);
            $document = new Document($html);

            $title = $this->getElementTextFromDocument($document, $this->settings->getTitleSelector());
            if ($title === null) {
                continue;
            }
            $text = $this->getMultiTextFromDocument($document, $this->settings->getTextSelector());
            $image = $this->getImageSrcFromDocument($document, $this->settings->getImageSelector());

            $result = [
                'SOURCE' => $this->settings->getSource(),
                'TITLE' => $title,
                'TEXT' => $text,
                'IMAGE_SRC' => $image
            ];

            $this->putResultToStorage($result);
            $counter++;
        }
    }

    /**
     * @param Document $document
     * @param string $selector
     * @return string|null
     * @throws InvalidSelectorException
     */
    protected function getElementTextFromDocument(Document $document, string $selector): ?string
    {
        $element = $document->first($selector);
        if (empty($element)) {
            return null;
        }

        return trim($element->text());
    }

    /**
     * @param Document $document
     * @param string $selector
     * @return string|null
     *
     * @throws InvalidSelectorException
     */
    protected function getMultiTextFromDocument(Document $document, string $selector): ?string
    {
        $elements = $document->find($selector);
        if (empty($elements)) {
            return null;
        }

        $fullText = null;
        /** @var Element $element */
        foreach ($elements as $element) {
            $elementText = $element->text();
            if (empty($elementText)) {
                continue;
            }
            $fullText .= ' ' . trim($elementText);
        }
        return $fullText;
    }

    /**
     * @param Document $document
     * @param string $selector
     *
     * @return string|null
     * @throws InvalidSelectorException
     */
    protected function getImageSrcFromDocument(Document $document, string $selector): ?string
    {
        $element = $document->first($selector);
        if (empty($element)) {
            return null;
        }

        $image = $element->getAttribute('src');

        return !empty($image) ? $image : null;
    }
}
