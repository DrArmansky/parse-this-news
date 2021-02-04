<?php


namespace ParseThisNews\Controller;


use ParseThisNews\Model\NewsModel;
use ParseThisNews\Parser\NewsParser;
use ParseThisNews\Parser\ResultStorage\NewsResultStorage;
use ParseThisNews\Repository\iRepository;
use ParseThisNews\Repository\NewsRepository;
use ParseThisNews\Util\Template;

class NewsController implements iRenderableController
{
    //Just for dev, not for prod
    private const PARSE_SOURCE = 'https://www.rbc.ru/';

    private iRepository $repository;
    private string $viewsPath;

    public function __construct()
    {
        $this->repository = new NewsRepository();
        $this->viewsPath = $_SERVER['DOCUMENT_ROOT'] . '/views';
    }

    /**
     * @throws \DiDom\Exceptions\InvalidSelectorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function newsListAction(): void
    {
        $this->parseNews(self::PARSE_SOURCE);
        $newsInfo = $this->getNewsInfoFromRepository();
        $data = ['NEWS_LIST' => $this->prepareNewsListData($newsInfo)];
        $this->renderAction($this->viewsPath . '/list.php', $data);
    }

    /**
     * @param string $source
     *
     * @throws \DiDom\Exceptions\InvalidSelectorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function parseNews(string $source): void
    {
        $parser = new NewsParser(new NewsResultStorage());
        $parser->parse($source);
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

        $newListData = [];
        /** @var NewsModel $news */
        foreach ($newsInfo as $news) {
            $newListData[$news->getId()]['LINK'] = $this->createNewsLink($news->getCode());
            $newListData[$news->getId()]['TITLE'] = $news->getTitle();
            $newListData[$news->getId()]['TEXT'] = substr($news->getText(), 0, 200) . '...';
        }

        return $newListData;
    }

    protected function prepareNewsDataByCode(string $code): array
    {
        $newsModel = $this->repository->get([NewsRepository::FIELD_CODE => $code]);
        if ($newsModel === null) {
            return [];
        }

        return [
            'NEWS_DATA' => [
                'TITLE' => $newsModel->getTitle(),
                'IMAGE' => $newsModel->getImage(),
                'TEXT' => $newsModel->getText()
            ]
        ];
    }

    protected function getNewsCodeFromRequest(): string
    {
        $uriAndParams = explode('?', $_SERVER['REQUEST_URI']);
        $uriElements = explode('/', reset($uriAndParams));
        return (string)end($uriElements);
    }

    protected function getNewsInfoFromRepository(): array
    {
        return $this->repository->getAll();
    }

    protected function createNewsLink(string $newsCode): string
    {
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/detail/' . $newsCode;
    }
}