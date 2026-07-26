<?php

use Matasar\StrikePlagiarism\CallbackWebhook;
use PHPUnit\Framework\TestCase;

final class CallbackWebhookTest extends TestCase
{
    public function testSuccessWebhook()
    {
        $payload = json_encode([
            'status' => 'success',
            'message' => 'Notification received',
            'id' => '123-abc'
        ]);

        $webhook = new CallbackWebhook($payload);

        $this->assertEquals('success', $webhook->getStatus());
        $this->assertEquals('Notification received', $webhook->getMessage());
        $this->assertEquals('123-abc', $webhook->getDocumentId());
        $this->assertFalse($webhook->isError());
        $this->assertTrue($webhook->isCompleted());
        $this->assertIsArray($webhook->toArray());
    }

    public function testErrorWebhook()
    {
        $payload = json_encode([
            'status' => 'error',
            'message' => 'Access denied'
        ]);

        $webhook = new CallbackWebhook($payload);

        $this->assertEquals('error', $webhook->getStatus());
        $this->assertEquals('Access denied', $webhook->getMessage());
        $this->assertNull($webhook->getDocumentId());
        $this->assertTrue($webhook->isError());
        $this->assertFalse($webhook->isCompleted());
    }

    public function testInvalidJsonThrowsException()
    {
        $this->expectException(\UnexpectedValueException::class);
        new CallbackWebhook('invalid json {');
    }
}
