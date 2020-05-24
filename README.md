# StrikePlagiarism API v2 client for PHP
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
    'en',
    'A test document',
    'Yevhen Matasar',
    'Yevhen Matasar',
    __DIR__ . '/test.pdf'
);

$response = $client->addDocument($document);

$report = $client->getReport($response['id']);
```

The content of ```$report``` will be:
```
{
  ["html"]=> "<html>...original html report...</html>",
  ["name"]=>
  string(15) "A test document"
  ["author"]=>
  string(14) "Yevhen Matasar"
  ["coordinator"]=>
  string(14) "Yevhen Matasar"
  ["similarity_1"]=>
  float(100)
  ["similarity_2"]=>
  float(0)
  ["phrase_length"]=>
  int(25)
  ["words_count"]=>
  int(20)
  ["chars_count"]=>
  int(110)
  ["sources"]=>
  array(1) {
    ["081e22a6707edb4e115f8bb033a73f9f"]=>
    array(3) {
      ["url"]=>
      string(59) "https://s1.q4cdn.com/806093406/files/doc_downloads/test.pdf"
      ["author"]=>
      string(2) " "
      ["similar_words"]=>
      int(20)
    }
  }
}
```

## Tests
To run tests you need to:
1. Get your own API key
2. Copy ```phpunit.xml.dist``` to ```phpunit.xml```
3. Edit ```phpunit.xml```, replace ```API_KEY``` with your own
4. Run ```vendor/bin/phpunit``` (you need to run ```composer install``` first).