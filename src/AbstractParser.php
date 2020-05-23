<?php

namespace Matasar\StrikePlagiarism;

abstract class AbstractParser
{
    /**
     * @var string|null
     */
    protected $content;

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return array
     */
    abstract public function parse(): array;
}
