<?php


namespace ParseThisNews\Controller;


use ParseThisNews\Model\ParserSettings;

class SettingsFormController extends BaseController
{
    //TODO:: temporary solution, need to move to own class
    private const LANG_NAMES = [
        'source' => 'Ресурс',
        'titleSelector' => 'Селектор заголовка',
        'linkSelector' => 'Селектор ссылки',
        'textSelector' => 'Селектор текста статьи',
        'imageSelector' => 'Селектор изображения',
        'newsLimit' => 'Количество выбираемых статьей'
    ];

    /**
     * @throws \JsonException
     */
    public function settingsFromAction(): void
    {
        $this->renderAction($this->viewsPath . '/form.php', [
            'ROUTES' => $this->getRoutes(),
            'FIELDS' => $this->getFormFields()
        ]);
    }

    protected function getFormFields(): array
    {
        $properties = (new \ReflectionClass(ParserSettings::class))->getProperties();
        return array_map(static function($property){
            return  [
                'NAME' => $property->getName(),
                'TYPE' => (string)$property->getType(),
                'LANG_NAME' => self::LANG_NAMES[$property->getName()]
            ];
        }, $properties);
    }

    /**
     * @return false|string
     * @throws \JsonException
     */
    protected function getRoutes()
    {
        $routes = [
            'PARSE' => '/api/v1/parse/',
            'SETTINGS' => '/api/v1/settings/',
            'SOURCES' => '/api/v1/sources/'
        ];
        return json_encode($routes, JSON_THROW_ON_ERROR);
    }
}