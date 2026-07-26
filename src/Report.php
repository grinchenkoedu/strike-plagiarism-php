<?php

namespace Matasar\StrikePlagiarism;

class Report
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->data['id'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->data['status'] ?? null;
    }

    /**
     * @return float|null
     */
    public function getSimilarity1(): ?float
    {
        return isset($this->data['similarity_1']) ? (float)$this->data['similarity_1'] : null;
    }

    /**
     * @return float|null
     */
    public function getSimilarity2(): ?float
    {
        return isset($this->data['similarity_2']) ? (float)$this->data['similarity_2'] : null;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->data['title'] ?? $this->data['name'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->data['author'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getCoordinator(): ?string
    {
        return $this->data['coordinator'] ?? null;
    }

    /**
     * Fetch a specific raw field by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Get the raw JSON array data.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
