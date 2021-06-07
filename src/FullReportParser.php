<?php

namespace Matasar\StrikePlagiarism;

use Sunra\PhpSimple\HtmlDomParser;

class FullReportParser extends AbstractParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(): array
    {
        if (!defined('MAX_FILE_SIZE')) {
            define('MAX_FILE_SIZE', PHP_INT_MAX);
        }

        $parser = HtmlDomParser::str_get_html($this->content);
        $info = $parser->find('.report-info p');

        $data = [
            'html' => $this->content,
            'name' => $parser->find('.report-title p', 0)->innertext(),
            'author' => $info[0]->innertext(),
            'coordinator' => $info[1]->innertext(),
            'similarity_1' => floatval($parser->find('.similarity-info .similarity-info-wp1 .percent', 0)->innertext()),
            'similarity_2' => floatval($parser->find('.similarity-info .similarity-info-wp2 .percent', 0)->innertext()),
            'citations' => floatval($parser->find('.similarity-info-citations .percent', 0)->innertext()),
            'phrase_length' => intval($parser->find('.similarity-info-desc .similarity-info-wp1 span', 0)->innertext()),
            'words_count' => intval($parser->find('.similarity-info-desc .similarity-info-wp2 span', 0)->innertext()),
            'chars_count' => intval($parser->find('.similarity-info-wpbap span', 0)->innertext()),
            'sources' => [],
        ];

        $sources = $parser->find('#active-longest-table tr');

        foreach ($sources as $source) {
            $values = $source->getElementsByTagName('td');

            if (empty($values)) {
                continue;
            }

            $a = $values[1]->getElementByTagName('a');

            if (null === $a) {
                continue;
            }

            $url = $a->getAttribute('href');
            $data['sources'][md5($url)] = [
                'url' => $url,
                'author' => trim(html_entity_decode($values[2]->innertext())),
                'similar_words' => intval($values[3]->innertext())
            ];
        }

        return $data;
    }
}
