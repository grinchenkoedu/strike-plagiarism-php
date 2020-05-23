<?php

namespace Matasar\StrikePlagiarism;

use simplehtmldom_1_5\simple_html_dom_node;
use Sunra\PhpSimple\HtmlDomParser;

class FullReportParser extends AbstractParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(): array
    {
        $parser = HtmlDomParser::str_get_html($this->content);


        /** @var simple_html_dom_node[] $rows */
        $rows = $parser->find('table.newMetric td');

        $data = [
            'html' => $this->content,
            'name' => trim($rows[0]->innertext()),
            'author' => trim($rows[1]->innertext()),
            'coordinator' => trim($rows[1]->innertext()),
            'similarity_1' => floatval($rows[4]->innertext()),
            'similarity_2' => floatval($rows[5]->innertext()),
            'phrase_length' => intval($rows[6]->innertext()),
            'words_count' => intval($rows[7]->innertext()),
            'chars_count' => intval($rows[8]->innertext()),
            'sources' => [],
        ];

        $sources = $parser->find('#topTenDiv tr');

        foreach ($sources as $source) {
            $row = $source->getElementsByTagName('td');

            if (count($row) < 4) {
                continue;
            }

            $a = $row[1]->getElementByTagName('a');
            $url = $a->getAttribute('href');

            $data['sources'][md5($url)] = [
                'url' => $url,
                'author' => trim(html_entity_decode($row[2]->innertext())),
                'similar_words' => intval($row[3]->innertext())
            ];
        }

        return $data;
    }
}
