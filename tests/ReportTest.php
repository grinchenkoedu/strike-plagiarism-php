<?php

use Matasar\StrikePlagiarism\Report;
use PHPUnit\Framework\TestCase;

final class ReportTest extends TestCase
{
    public function testReportGetters()
    {
        $data = [
            'id' => '12345',
            'status' => 'success',
            'similarity_1' => 12.5,
            'similarity_2' => 5.0,
            'title' => 'Test Report',
            'author' => 'John Doe',
            'coordinator' => 'Jane Doe',
            'unknown_field' => 'extra_data'
        ];

        $report = new Report($data);

        $this->assertEquals('12345', $report->getId());
        $this->assertEquals('success', $report->getStatus());
        $this->assertEquals(12.5, $report->getSimilarity1());
        $this->assertEquals(5.0, $report->getSimilarity2());
        $this->assertEquals('Test Report', $report->getTitle());
        $this->assertEquals('John Doe', $report->getAuthor());
        $this->assertEquals('Jane Doe', $report->getCoordinator());
        
        // Test arbitrary getter
        $this->assertEquals('extra_data', $report->get('unknown_field'));
        $this->assertNull($report->get('missing_field'));
        $this->assertEquals('default', $report->get('missing_field', 'default'));
        
        // Test toArray
        $this->assertEquals($data, $report->toArray());
    }

    public function testReportMissingFieldsReturnNull()
    {
        $report = new Report([]);

        $this->assertNull($report->getId());
        $this->assertNull($report->getStatus());
        $this->assertNull($report->getSimilarity1());
        $this->assertNull($report->getSimilarity2());
        $this->assertNull($report->getTitle());
        $this->assertNull($report->getAuthor());
        $this->assertNull($report->getCoordinator());
    }

    public function testTitleFallbackToName()
    {
        $report = new Report([
            'name' => 'Fallback Title'
        ]);

        $this->assertEquals('Fallback Title', $report->getTitle());
    }
}
