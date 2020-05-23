<?php

namespace Matasar\StrikePlagiarism;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

class Client
{
    const HOST_DEFAULT = 'lmsapi.plagiat.pl';

    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @param string $apiKey
     * @param bool $ssl
     * @param string|null $host
     */
    public function __construct(string $apiKey, bool $ssl = true, string $host = null)
    {
        $this->client = new HttpClient([
            'base_uri' => $this->getBaseUri($ssl, $host)
        ]);

        $this->apiKey = $apiKey;
    }

    /**
     * @param Document $document
     *
     * @return array
     *
     * @throws RequestException
     */
    public function addDocument(Document $document): array
    {
        $data = $document->getData();
        $multipart = [
            [
                'name' => 'file',
                'contents' => fopen($data['fileUri'], 'r'),
                'filename' => basename($data['fileUri'])
            ]
        ];

        unset($data['fileUri']);

        foreach ($data as $name => $value) {
            $multipart[] = [
                'name' => $name,
                'contents' => $value
            ];
        }

        return $this->sendMultipartRequest('documents/add', $multipart);
    }

    /**
     * @param string $id
     *
     * @return array
     *
     * @throws RequestException
     */
    public function getDocument(string $id): array
    {
        return $this->sendGetRequest('documents', [
            'id' => $id
        ]);
    }

    /**
     * @param string $id
     *
     * @return array
     *
     * @throws RequestException
     */
    public function getReport(string $id): array
    {
        return $this->sendGetRequest(
            'documents/report',
            [
                'id' => $id
            ],
            new FullReportParser()
        );
    }

    /**
     * @param string $id
     *
     * @return array
     *
     * @throws RequestException
     */
    public function removeDocument(string $id): array
    {
        return $this->sendDocumentRequest($id, 'documents/remove');
    }

    /**
     * @param string $documentId
     *
     * @return array
     *
     * @throws RequestException
     */
    public function addToReferences(string $documentId): array
    {
        return $this->sendDocumentRequest($documentId, 'documents/add-to-database');
    }

    /**
     * @param string $documentId
     *
     * @return array
     *
     * @throws RequestException
     */
    public function removeFromReferences(string $documentId): array
    {
        return $this->sendDocumentRequest($documentId, 'documents/remove-from-database');
    }

    /**
     * @param string $documentId
     * @param string $path
     *
     * @return array
     *
     * @throws RequestException
     */
    protected function sendDocumentRequest(string $documentId, string $path): array
    {
        $multipart = [
            [
                'name' => 'id',
                'contents' => $documentId
            ]
        ];

        return $this->sendMultipartRequest($path, $multipart);
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param AbstractParser $parser
     *
     * @return array
     *
     * @throws RequestException
     */
    protected function sendGetRequest(string $path, array $parameters, AbstractParser $parser = null): array
    {
        $data = [];

        $parameters['APIKEY'] = $this->apiKey;

        try {
            $response = $this->client->get($this->getBaseUri() . $path, [
                'query' => $parameters
            ]);

            $data = $this->getResponseData($response, $parser);
        } catch (\Exception $ex) {
            $this->handleException($ex);
        }

        return $data;
    }

    /**
     * @param string $path
     * @param array $multipart
     *
     * @return array
     *
     * @throws RequestException
     */
    protected function sendMultipartRequest(string $path, array $multipart): array
    {
        $data = [];

        $multipart[] = [
            'name' => 'APIKEY',
            'contents' => $this->apiKey
        ];

        try {
            $response = $this->client->post($this->getBaseUri() . $path, [
                'multipart' => $multipart
            ]);

            $data = $this->getResponseData($response);
        } catch (\Exception $ex) {
            $this->handleException($ex);
        }

        return $data;
    }

    /**
     * @param ResponseInterface $response
     * @param AbstractParser|null $parser
     *
     * @return array
     */
    protected function getResponseData(ResponseInterface $response, AbstractParser $parser = null): array
    {
        if (null !== $parser) {
            $parser->setContent($response->getBody()->getContents());

            return $parser->parse();
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (!is_array($data)) {
            throw new \UnexpectedValueException('Malformed response, JSON expected!');
        }

        return $data;
    }

    /**
     * @param bool $ssl
     * @param string|null $host
     *
     * @return string
     */
    protected function getBaseUri(bool $ssl = true, string $host = null): string
    {
        return sprintf('%s://%s/api/v2/',
            $ssl ? 'https' : 'http',
            $host ?? self::HOST_DEFAULT
        );
    }

    /**
     * @param \Exception $ex
     *
     * @throws RequestException
     */
    protected function handleException(\Exception $ex)
    {
        if ($ex instanceof ClientException) {
            $response = $ex->getResponse();
            $body = $response->getBody()->getContents();

            throw new RequestException(
                $ex->getMessage(),
                is_array($body) ? $body : null,
                $response->getStatusCode()
            );
        }

        throw new RequestException($ex->getMessage());
    }
}
