<?php

namespace Matasar\StrikePlagiarism;

class RequestException extends \Exception
{
    /**
     * @var int|null
     */
    protected $statusCode;

    /**
     * @var array|null
     */
    protected $data;

    public function __construct(string $message = "", array $data = null, int $statusCode = null)
    {
        $this->statusCode = $statusCode;
        $this->data = $data;

        parent::__construct($message);
    }

    /**
     * @return int|null
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }
}
