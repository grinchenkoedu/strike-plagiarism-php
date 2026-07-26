<?php

namespace Matasar\StrikePlagiarism;

class CallbackWebhook
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param string $jsonPayload
     * @throws \UnexpectedValueException
     */
    public function __construct(string $jsonPayload)
    {
        $data = json_decode($jsonPayload, true);

        if (!is_array($data)) {
            throw new \UnexpectedValueException('Malformed JSON webhook payload!');
        }

        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->data['status'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->data['message'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getDocumentId(): ?string
    {
        return $this->data['id'] ?? $this->data['documentId'] ?? null;
    }

    /**
     * Check if the webhook represents an error.
     *
     * @return bool
     */
    public function isError(): bool
    {
        return $this->getStatus() === 'error';
    }

    /**
     * Check if the document processing was successfully completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->getStatus() === 'success' || $this->getStatus() === 'completed';
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
