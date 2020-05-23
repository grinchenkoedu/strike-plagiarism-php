<?php

use Matasar\StrikePlagiarism\Client;
use Matasar\StrikePlagiarism\Document;
use Matasar\StrikePlagiarism\RequestException;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (empty($this->getApiKey())) {
            throw new UnexpectedValueException('API_KEY must be set, check the phpunit.xml!');
        }

        parent::setUp();
    }

    /**
     * @throws \Matasar\StrikePlagiarism\RequestException
     */
    public function testUploadDocument()
    {
        $client = $this->getClient();

        $document = new Document(
            'en',
            'A test document',
            'Yevhen Matasar',
            'Yevhen Matasar',
            __DIR__ . '/test.pdf'
        );

        $data = $client->addDocument($document);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('new', $data['status']);

        $data = $client->getDocument($data['id']);

        $this->assertEquals($document->getTitle(), $data['title']);
        $this->assertEquals($document->getAuthor(), $data['author']);
        $this->assertEquals($document->getCoordinator(), $data['coordinator']);
        $this->assertArrayHasKey('indexed', $data);
        $this->assertArrayHasKey('document-size', $data);
        $this->assertArrayHasKey('md5sum', $data);
    }

    public function testException()
    {
        try {
            $this->getClient()->getDocument('fake_document_id');
        } catch (Throwable $exception) {
            $this->assertEquals(RequestException::class, get_class($exception));
        }
    }

    /**
     * @return string
     */
    private function getApiKey(): string
    {
        return trim(getenv('API_KEY'));
    }

    /**
     * @return Client
     */
    private function getClient(): Client
    {
        static $client;

        if (null === $client) {
            $client = new Client($this->getApiKey());
        }

        return $client;
    }
}
