<?php

namespace Matasar\StrikePlagiarism;

trait DataTrait
{
    /**
     * @return array
     */
    public function getData(): array
    {
        return array_filter(
            get_object_vars($this),
            function ($val) {
                return $val !== null;
            }
        );
    }
}
