<?php


namespace ParseThisNews\Controller;


interface iRenderableController
{
    public function renderAction(string $templatePath, array $data);
}
