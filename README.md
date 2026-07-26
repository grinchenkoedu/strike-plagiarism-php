# StrikePlagiarism API v2 client for PHP

[![Tests](https://github.com/grinchenkoedu/strike-plagiarism-php/actions/workflows/tests.yml/badge.svg)](https://github.com/grinchenkoedu/strike-plagiarism-php/actions/workflows/tests.yml)
[![PHP version](https://img.shields.io/badge/PHP-%3E%3D7.4-8892BF.svg)](https://php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

An **unofficial** StrikePlagiarism API v2 client. 

## How to install
This is a [composer](https://getcomposer.org/doc/00-intro.md) package, to install simply run:
```
composer require matasarei/strike-plagiarism-php
```

## How to use
```php
use Matasar\StrikePlagiarism\Client;
use Matasar\StrikePlagiarism\Document;

$client = new Client('YOUR_API_KEY');

$document = new Document(
    'en', // language code (ISO 639-1)
    'A test document', // title
    'Yevhen Matasar', // author
    'Yevhen Matasar', // coordinator
    __DIR__ . '/test.pdf' // file uri
);

// Optional: Add specific metadata and configuration
$document->setCallbackUrl('https://your-site.com/webhook');
$document->setAiDetection(true);
$document->setGrammarCheck(true);
$document->setUserId('student-123');

$response = $client->addDocument($document);

// Fetch the report as a DTO (use `true` for a short report)
$report = $client->getReport($response['id'], short: false);
```

The content of ```$report``` will be an instance of the `Matasar\StrikePlagiarism\Report` class, which provides convenient getters for the API's JSON response, for example:
```php
echo $report->getId(); // "12345-abcde"
echo $report->getStatus(); // "success"
echo $report->getSimilarity1(); // 10.5
echo $report->getSimilarity2(); // 0.0
echo $report->getTitle(); // "A test document"
echo $report->getAuthor(); // "Yevhen Matasar"
echo $report->getCoordinator(); // "Yevhen Matasar"

// To access any other properties from the raw response:
$status = $report->get('status');

// Or to get the entire raw JSON response as an array:
$rawData = $report->toArray();

// If you need the raw HTML report directly (as a string) instead of JSON:
$htmlString = $client->getReportHtml($response['id']);

// You can also fetch the report as a PDF:
$pdfString = $client->getReportPdf($response['id']);
```

### Webhooks (Callbacks)
When a document finishes processing, the API can send a notification to your `callbackUrl`. You can parse this easily:
```php
use Matasar\StrikePlagiarism\CallbackWebhook;

$payload = file_get_contents('php://input');
$webhook = new CallbackWebhook($payload);

if ($webhook->isCompleted()) {
    $reportId = $webhook->getDocumentId();
    // fetch report...
}
```

### Other Endpoints
The client supports many other operations out of the box:
```php
$client->rejectDocument('doc-id');
$client->sendForCorrection('doc-id');
$client->getProtocols('doc-id');

// Add/remove from reference database
$client->addToReferences('doc-id');
$client->removeFromReferences('doc-id');
$client->removeDocument('doc-id');
```

## Tests
To run tests locally, you can use Docker. Tests are mocked and do not make actual API requests.

1. Install dependencies using Docker:
   ```bash
   docker run --rm -v $(pwd):/app -w /app composer:latest composer install
   ```
2. Run PHPUnit using Docker:
   ```bash
   docker run --rm -v $(pwd):/app -w /app composer:latest vendor/bin/phpunit
   ```