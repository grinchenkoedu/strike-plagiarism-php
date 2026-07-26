<?php

use Matasar\StrikePlagiarism\Document;
use PHPUnit\Framework\TestCase;

final class DocumentTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $document = new Document('en', 'Title', 'Author', 'Coordinator', '/path/to/file');
        
        $this->assertEquals('en', $document->getLanguageCode());
        $this->assertEquals('Title', $document->getTitle());
        $this->assertEquals('Author', $document->getAuthor());
        $this->assertEquals('Coordinator', $document->getCoordinator());
        $this->assertEquals('/path/to/file', $document->getFileUri());
        
        $document->setFaculty('Engineering');
        $this->assertEquals('Engineering', $document->getFaculty());
        
        $document->setReviewer('Reviewer');
        $this->assertEquals('Reviewer', $document->getReviewer());
        
        $document->setId('123');
        $this->assertEquals('123', $document->getId());
        
        $document->setAction(Document::ACTION_INDEX);
        $this->assertEquals(Document::ACTION_INDEX, $document->getAction());
        
        $document->setDocumentKind(Document::KIND_ARTICLE);
        $this->assertEquals(Document::KIND_ARTICLE, $document->getDocumentKind());

        $document->setCallbackUrl('https://example.com/webhook');
        $this->assertEquals('https://example.com/webhook', $document->getCallbackUrl());

        $document->setUserId('user123');
        $this->assertEquals('user123', $document->getUserId());

        $document->setAssignmentId('assign456');
        $this->assertEquals('assign456', $document->getAssignmentId());

        $document->setAiDetection(true);
        $this->assertTrue($document->getAiDetection());

        $document->setGrammarCheck(false);
        $this->assertFalse($document->getGrammarCheck());
    }

    public function testInvalidLanguageCodeThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Language code must be ISO 639-1 compatible');
        
        new Document('eng', 'Title', 'Author', 'Coordinator', '/path');
    }

    public function testInvalidActionThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $document = new Document('en', 'Title', 'Author', 'Coordinator', '/path');
        $document->setAction('invalid_action');
    }

    public function testGetDataFiltersNullValues()
    {
        $document = new Document('en', 'Title', 'Author', 'Coordinator', '/path');
        $data = $document->getData();
        
        $this->assertArrayNotHasKey('faculty', $data);
        $this->assertArrayNotHasKey('id', $data);
        $this->assertArrayNotHasKey('reviewer', $data);
        $this->assertEquals('en', $data['languageCode']);
        $this->assertEquals(Document::ACTION_CHECK, $data['action']);
    }
}
