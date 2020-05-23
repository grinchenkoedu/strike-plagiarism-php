<?php

namespace Matasar\StrikePlagiarism;

trait ValidationTrait
{
    /**
     * @param $languageCode
     *
     * @return string
     */
    protected function validateLanguageCode($languageCode): string
    {
        if (preg_match('/^[a-z]{2}$/', $languageCode)) {
            return $languageCode;
        }

        throw new \InvalidArgumentException('Language code must be ISO 639-1 compatible');
    }
}
