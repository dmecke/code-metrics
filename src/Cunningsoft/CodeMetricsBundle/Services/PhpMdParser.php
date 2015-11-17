<?php

namespace Cunningsoft\CodeMetricsBundle\Services;

class PhpMdParser extends AbstractMetricsParser
{
    /**
     * @return string
     */
    protected function getMetricsFilename()
    {
        return 'phpmd.xml';
    }
}
