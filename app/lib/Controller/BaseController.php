<?php


namespace ParseThisNews\Controller;


use ParseThisNews\Util\Template;

abstract class BaseController implements iRenderableController
{
    protected string $viewsPath;

    public function __construct()
    {
        $this->viewsPath = $_SERVER['DOCUMENT_ROOT'] . '/views';
    }

    public function renderAction(string $templatePath, array $data): void
    {
        echo Template::render($templatePath, $data);
    }
}