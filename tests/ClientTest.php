<?php

use Matasar\StrikePlagiarism\Client;
use Matasar\StrikePlagiarism\Document;
use Matasar\StrikePlagiarism\RequestException;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

final class ClientTest extends TestCase
{
    /**
     * @var MockHandler
     */
    private $mockHandler;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockHandler = new MockHandler();
    }

    /**
     * @throws \Matasar\StrikePlagiarism\RequestException
     */
    public function testUploadDocument()
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['id' => '123', 'status' => 'new'])),
            new Response(200, [], json_encode([
                'title' => 'A test document',
                'author' => 'Yevhen Matasar',
                'coordinator' => 'Yevhen Matasar',
                'indexed' => true,
                'document-size' => 1000,
                'md5sum' => 'somehash'
            ]))
        );

        $client = $this->getClient();

        $document = new Document(
            'en',
            'A test document',
            'Yevhen Matasar',
            'Yevhen Matasar',
            __FILE__
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
        $this->mockHandler->append(
            new Response(404, [], json_encode(['message' => 'Not found']))
        );

        try {
            $this->getClient()->getDocument('fake_document_id');
        } catch (Throwable $exception) {
            $this->assertEquals(RequestException::class, get_class($exception));
        }
    }

    public function testGetReport()
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'id' => '12345-abcde',
                'status' => 'success',
                'similarity_1' => 10.5,
                'similarity_2' => 0.0,
                'title' => 'A test document',
                'author' => 'Yevhen Matasar',
                'coordinator' => 'Yevhen Matasar'
            ]))
        );

        $client = $this->getClient();
        $report = $client->getReport('12345-abcde');

        $this->assertInstanceOf(\Matasar\StrikePlagiarism\Report::class, $report);
        $this->assertEquals('12345-abcde', $report->getId());
        $this->assertEquals('success', $report->getStatus());
        $this->assertEquals(10.5, $report->getSimilarity1());
        $this->assertEquals(0.0, $report->getSimilarity2());
        $this->assertEquals('A test document', $report->getTitle());
        $this->assertEquals('Yevhen Matasar', $report->getAuthor());
        $this->assertEquals('Yevhen Matasar', $report->getCoordinator());
        $this->assertIsArray($report->toArray());
    }

    public function testGetReportHtml()
    {
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'text/html'], '<html><body>Report content</body></html>')
        );

        $client = $this->getClient();
        $html = $client->getReportHtml('12345-abcde');

        $this->assertEquals('<html><body>Report content</body></html>', $html);
    }

    public function testGetReportPdf()
    {
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/pdf'], '%PDF-1.4...')
        );

        $pdf = $this->getClient()->getReportPdf('12345');
        $this->assertEquals('%PDF-1.4...', $pdf);
    }

    public function testRejectDocument()
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['status' => 'success']))
        );

        $result = $this->getClient()->rejectDocument('12345');
        $this->assertEquals('success', $result['status']);
    }

    public function testSendForCorrection()
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['status' => 'success']))
        );

        $result = $this->getClient()->sendForCorrection('12345');
        $this->assertEquals('success', $result['status']);
    }

    public function testGetProtocols()
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['status' => 'success', 'protocols' => []]))
        );

        $result = $this->getClient()->getProtocols('12345');
        $this->assertEquals('success', $result['status']);
        $this->assertIsArray($result['protocols']);
    }

    /**
     * @return Client
     */
    private function getClient(): Client
    {
        $handlerStack = HandlerStack::create($this->mockHandler);
        $guzzleClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);

        $client = new Client('fake_api_key');
        
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('client');
        if (\PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue($client, $guzzleClient);

        return $client;
    }
}
