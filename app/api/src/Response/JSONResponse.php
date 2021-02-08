<?php


namespace ParseThisNewsApi\Response;


class JSONResponse implements iResponse
{
    private const JSON_RESPONSE_HEADER = 'Content-Type: application/json';

    protected int $statusCode;
    protected array $content;

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }

    public function send(): void
    {
        header(self::JSON_RESPONSE_HEADER);
        try {
            $json = json_encode($this->content, JSON_THROW_ON_ERROR);
            http_response_code($this->statusCode);
        } catch (\JsonException $exception) {
            http_response_code(500);
            $json = ['error' => $exception->getMessage()];
        }
        echo $json;
    }
}