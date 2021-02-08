<?php

namespace ParseThisNews\Parser\Service;

use DiDom\Document;
use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use ParseThisNews\Model\News;
use ParseThisNews\Model\ParserSettings;


class NewsParser implements iParser
{
    protected ParserSettings $settings;

    public function __construct(ParserSettings $parserSettings)
    {
        $this->settings = $parserSettings;
    }

    /**
     * @return News[]
     *
     * @throws GuzzleException
     * @throws InvalidSelectorException
     */
    public function parse(): array
    {
        $html = $this->getHtmlFromSource($this->settings->getSource());
        $links = $this->getLinksFromDocument(new Document($html));

        return $this->parseContentByLinks($links);
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
     * @return News[]
     *
     * @throws GuzzleException
     * @throws InvalidSelectorException
     */
    protected function parseContentByLinks(array $links): array
    {
        if ($this->settings === null) {
            throw new \RuntimeException('Parsing settings are not defined');
        }

        $counter = 0;
        $result = [];

        foreach ($links as $link) {
            if ($counter === $this->settings->getNewsLimit()) {
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

            $result[] = (new News())
                ->setSource($this->settings->getSource())
                ->setTitle($title)
                ->setText($text)
                ->setImage($image);

            $counter++;
        }

        return $result;
    }

    /**
     * @param Document $document
     * @param string $selector
     * @return string|null
     *
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
     * @return string|null
     *
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
