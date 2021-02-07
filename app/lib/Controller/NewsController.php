<?php


namespace ParseThisNews\Controller;


use ParseThisNews\Model\News;
use ParseThisNews\Parser\Manager;
use ParseThisNews\Repository\iRepository;
use ParseThisNews\Repository\NewsRepository;
use ParseThisNews\Storage\MySQLStorage;
use ParseThisNews\Util\Settings;
use ParseThisNews\Util\Template;

class NewsController implements iRenderableController
{
    //Just for dev, not for prod
    private const PARSE_SOURCE = 'https://www.rbc.ru/';
    private const PREVIEW_SETTINGS_NAME = 'Preview';

    private iRepository $newsRepository;
    private string $viewsPath;

    public function __construct()
    {
        $this->newsRepository = new NewsRepository(new MySQLStorage());
        $this->viewsPath = $_SERVER['DOCUMENT_ROOT'] . '/views';
    }

    public function newsListAction(): void
    {
        $news = $this->getNewsInfoFromRepository(self::PARSE_SOURCE);
        if (empty($news)) {
            $news = (new Manager())->parseNewsFromResource(self::PARSE_SOURCE);
            $this->saveParsedNews($news);
        }
        //TODO:: remember! first rendering without code
        $data = ['NEWS_LIST' => $this->prepareNewsListData($news)];
        $this->renderAction($this->viewsPath . '/list.php', $data);
    }

    public function newsDetailAction(): void
    {
        $newsCode = $this->getNewsCodeFromRequest();
        $data = $this->prepareNewsDataByCode($newsCode);
        if (empty($data)) {
            \header("HTTP/1.0 404 Not Found");
            die();
        }

        $this->renderAction($this->viewsPath . '/detail.php', $data);
    }

    public function renderAction(string $templatePath, array $data): void
    {
        echo Template::render($templatePath, $data);
    }

    protected function prepareNewsListData(array $newsInfo): array
    {
        if (empty($newsInfo)) {
            return [];
        }

        $previewTextLength = Settings::getSettings(self::PREVIEW_SETTINGS_NAME)['preview_text_length'];
        $newListData = [];

        /** @var News $news */
        foreach ($newsInfo as $news) {
            $newListData[$news->getId()]['LINK'] = $this->createNewsLink($news->getCode());
            $newListData[$news->getId()]['TITLE'] = $news->getTitle();
            $newListData[$news->getId()]['TEXT'] = mb_strimwidth(
                $news->getText(),
                0,
                $previewTextLength,
                '...'
            );
        }

        return $newListData;
    }

    protected function prepareNewsDataByCode(string $code): array
    {
        $newsFromRepository = $this->newsRepository->get([NewsRepository::FIELD_CODE => $code]);
        if (empty($newsFromRepository)) {
            return [];
        }

        $news = reset($newsFromRepository);

        return [
            'NEWS_DATA' => [
                'TITLE' => $news->getTitle(),
                'IMAGE' => $news->getImage(),
                'TEXT' => $news->getText()
            ]
        ];
    }

    protected function getNewsCodeFromRequest(): string
    {
        $uriAndParams = explode('?', $_SERVER['REQUEST_URI']);
        $uriElements = explode('/', reset($uriAndParams));
        return (string)end($uriElements);
    }

    /**
     * @param string $source
     * @return News[]
     */
    protected function getNewsInfoFromRepository(string $source): array
    {
        return $this->newsRepository->get([NewsRepository::FIELD_SOURCE => $source]);
    }

    /**
     * @param News[] $news
     */
    protected function saveParsedNews(array $news): void
    {
        if (empty($news)) {
            return;
        }

        foreach ($news as $newsItem) {
            $this->newsRepository->add($newsItem);
        }
    }

    protected function createNewsLink(string $newsCode): string
    {
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/detail/' . $newsCode;
    }
}